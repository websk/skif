<?php
/**
 * @var $field_name string
 * @var $field_value string
 * @var $date_format string
 * @var $datetimepicker_options string
 */
?>

<div class="input-group" id="<?=$field_name?>">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
    <input name="<?=$field_name?>" type="text" value="<?=$field_value?>" class="form-control">
</div>

<script type="text/javascript">
    $(function () {
        $('#<?=$field_name?>').datetimepicker({
            format: '<?=$date_format?>',
            <?=$datetimepicker_options?>
        });
    });
</script>