<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoUploadData.php");

if (!isset($_POST["uploadButton"])) {
    echo "No file sent to page.";
    exit();
}

$videoUploadData = new VideoUploadData(
    $_POST["fileInput"], 
    $_POST["titleInput"], 
    $_POST["descriptionInput"], 
    $_POST["privacyInput"],
    $_POST["categoriesInput"],
    $_POST["THIS-USER"]
);

$videoProcessor = new VideoProcessor($con);
$wasSuccessful = $videoProcessor->upload(videoUploadData);

?>

