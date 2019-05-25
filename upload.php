<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
?>
    <div class="column">
    <?php
        $formProvider = new VideoDetailsFormProvider($con);
        echo $formProvider->createUploadForm();

        
    ?>
    </div>

    <script>
    $("form").submit(function() {
        $("#loadingModel").modal("show");
    });
    </script>

    <div class="modal fade" id="loadingModel" tabindex="-1" role="dialog" aria-labelledby="loadingModel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    Please wait.. this might take a while
                    <img src="assets/icons/loading-spinner.gif">
                </div>
            </div>
        </div>
    </div>
<?php require_once("includes/footer.php"); ?>