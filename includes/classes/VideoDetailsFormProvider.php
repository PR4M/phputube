<?php

class VideoDetailsFormProvider 
{
    public function createUploadForm()
    {
        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput();
        $descriptionInput = $this->createDescriptionInput();
        $privacyInput = $this->createPrivacyInput();

        return "<form action='processing.php' method='POST'>
                    $fileInput
                    $titleInput
                    $descriptionInput
                    $privacyInput
                </form>";
    }

    private function createFileInput()
    {
        return "<div class='form-group'>
                    <label for='uploadVideo'>Upload Video</label>
                    <input name='fileInput' type='file' class='form-control-file' id='uploadVideo' required>
                </div>";
    }

    private function createTitleInput()
    {
        return "<div class='form-group'>
                    <input name='titleInput' class='form-control' type='text' placeholder='Judul Video'>
                </div>";
    }

    private function createDescriptionInput()
    {
        return "<div class='form-group'>
                    <textarea name='descriptionInput' class='form-control' rows='3' type='text' placeholder='Judul Video'></textarea>
                </div>";
    }

    private function createPrivacyInput()
    {
        return "
                <div class='form-group'>
                    <select name='privacyInput' class='form-control'>
                        <option value='0'>0</option>
                        <option value='1'>1</option>
                    </select>
                </div>
                ";
    }
}