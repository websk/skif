<?php
/**
 * @var $field_name string
 * @var $field_value string
 * @var $date_format string
 * @var $datetimepicker_options string
 */
?>

<div class="input-group" id="<?php echo $field_name; ?>">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
    <input name="<?php echo $field_name; ?>" type="text" value="<?php echo $field_value; ?>" class="form-control">
</div>

<script type="text/javascript">
    $(function () {
        $('#<?php echo $field_name; ?>').datetimepicker({
            locale: 'ru',
            allowInputToggle: true,
            format: '<?php echo $date_format; ?>',
            <?php echo $datetimepicker_options; ?>
        });
    });
</script>