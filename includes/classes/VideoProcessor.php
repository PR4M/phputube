<?php
require_once("includes/header.php");
require_once("includes/classes/VideoUploadData.php");

class VideoProcessor
{
    private $con;
    private $sizeLimit = 500000000000;
    private $allowedTypes = array("mp4", "flv", "webm", "mkv", "avi", "wmv", "mov", "mpeg", "mpg");
    private $ffmpegPath;

    public function __construct($con)
    {
        $this->con = $con;
        $this->ffmpegPath = realpath("ffmpeg/bin/ffmpeg.exe");
    }

    public function upload($videoUploadData)
    {
        $targetDir = "uploads/videos/";
        $videoData = $videoUploadData->videoDataArray;
        
        // When the video is being upload, its name is changing temporary
        // Any space in video name will be replaced with "_" / underscore
        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
        $tempFilePath = str_replace(" ", "_", $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);

        if (! $isValidData) {
            return false;
        }

        if (move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            $finalFilePath = $targetDir . uniqid() . ".mp4";

            if (! $this->insertVideoData($videoUploadData, $finalFilePath)) {
                echo "Insert Query Failed";
                return false;
            }

            if (! $this->convertVideoToMP4($tempFilePath, $finalFilePath)) {
                echo "Convert Video Failed\n";
                return false;
            }

            if (! $this->deleteOriginalFile($tempFilePath)) {
                echo "Can't\n";
                return false;
            }
        }
    }

    private function processData($videoData, $filePath)
    {
        $videoType = pathinfo($filePath, PATHINFO_EXTENSION);

        if (! $this->isValidSize($videoData)) {
            echo "File too large. Can't be more than " . $this->sizeLimit . " bytes";
            return false;
        } else if (! $this->isValidType($videoType)) {
            echo "Invalid file type";
            return false;
        } else if ($this->hasError($videoData)) {
            echo "Error code: " . $videoData["error"];
            return false;
        }

        return true;
    }

    private function isValidSize($data) {
        return $data["size"] <= $this->sizeLimit;
    }

    private function isValidType($type) {
        $lowerCased = strtolower($type);
        return in_array($lowerCased, $this->allowedTypes);
    }

    private function hasError($data) {
        return $data["error"] != 0;
    }

    private function insertVideoData($uploadData, $filePath) 
    {
        $query = $this->con->prepare("INSERT INTO videos(title, uploadedBy, description, privacy, category, filePath)
            VALUES(:title, :uploadedBy, :description, :privacy, :category, :filePath)
        ");

        $query->bindParam(":title", $uploadData->title);
        $query->bindParam(":uploadedBy", $uploadData->uploadedBy);
        $query->bindParam(":description", $uploadData->description);
        $query->bindParam(":privacy", $uploadData->privacy);
        $query->bindParam(":category", $uploadData->category);
        $query->bindParam(":filePath", $filePath);

        return $query->execute();
    }

    public function convertVideoToMP4($tempFilePath, $finalFilePath)
    {
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";

        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);

        if ($returnCode != 0) {
            foreach ($outputLog as $line) {
                echo $line . "<br>";
            }
            return false;
        }

        return true;
    }

    private function deleteOriginalFile($filePath)
    {
        if (! unlink($filePath)) {
            echo "Couldn't not delete file\n";
            return false;
        }

        return true;
    }

}