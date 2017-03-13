<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

$bower_path = \Skif\Conf\ConfWrapper::value('bower_path');

?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10" class="form-control"><?php echo $text ?></textarea>

<script>
    CKEDITOR.replace('<?php echo $editor_name ?>', {
        toolbar: [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ]},
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList'] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'others', items: [ '-' ] }
        ],
        pasteFilter: 'plain-text',
        customConfig:  '/vendor/websk/skif/assets/js/ckeditor_config.js',
        contentsCss: ['<?php echo $bower_path; ?>/assets/libraries/bootstrap/css/bootstrap.min.css', '/assets/styles/main.css', '/assets/styles/style.css'],
        height: <?php echo $height ?>
    });
</script>
