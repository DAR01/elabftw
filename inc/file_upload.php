<?php
/**
 * inc/file_upload.php
 *
 * @author Nicolas CARPi <nicolas.carpi@curie.fr>
 * @copyright 2012 Nicolas CARPi
 * @see http://www.elabftw.net Official website
 * @license AGPL-3.0
 */
use \Elabftw\Elabftw\Tools as Tools;

// where are we, on experiments or items (database) page ?
if (strpos($_SERVER['SCRIPT_FILENAME'], 'experiments')) {
    $type = 'experiments';
} else {
    $type = 'items';
}
?>
<section class='box'>
    <!-- FILE UPLOAD BLOCK -->
    <script src="js/dropzone/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" media="all" href="js/dropzone/dist/min/dropzone.min.css" />
    <!-- fix some css here -->
    <style>
    .dropzone {
        border: 1px solid #fff;
    }
    .dropzone:hover {
        border: 1px solid #29AEB9;
        border-radius: 5px;
    }
    </style>
    <img src='img/attached.png' class='bot5px'> <h3 style='display:inline'><?php echo _('Attach a file'); ?></h3>
    <form action="app/upload.php"
        class="dropzone"
        id='elabftw-dropzone'>
    </form>
</section>
<script>
// we need this to reload the #filesdiv (div displaying uploaded files)
var type = '<?php echo $type; ?>';
if (type == 'items') {
    type = 'database';
}
var item_id = '<?php echo $id; ?>';

// config for dropzone, id is camelCased.
Dropzone.options.elabftwDropzone = {
    // i18n message to user
    dictDefaultMessage: '<?php echo _('Drop files here to upload'); ?>',
    maxFilesize: '<?php echo Tools::returnMaxUploadSize(); ?>', // MB
    init: function() {

        // add additionnal parameters (id and type)
        this.on("sending", function(file, xhr, formData) {
            formData.append("item_id", "<?php echo $id; ?>");
            formData.append("type", "<?php echo $type; ?>");
        });

        // once it is done
        this.on("complete", function(answer) {
            // check the answer we get back from app/uploads.php
            if (answer.xhr.responseText != 0) {
                alert("Upload failed: " + answer.xhr.responseText);
            }
            // reload the #filesdiv once the file is uploaded
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                $("#filesdiv").load(type + '.php?mode=edit&id=' + item_id + ' #filesdiv', function() {
                    // make the comment zone editable (fix issue #54)
                    $('.thumbnail p.editable').editable('app/editinplace.php', {
                     indicator : 'Saving...',
                     id   : 'id',
                     name : 'filecomment',
                     submit : 'Save',
                     cancel : 'Cancel',
                     style : 'display:inline'
                    });
                });
            }
        });
    }
};
</script>
