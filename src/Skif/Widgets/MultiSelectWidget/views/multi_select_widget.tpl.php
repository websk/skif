<?php
/**
 * @var $field_name string
 * @var $field_value array
 * @var $values_arr
 */

$bower_path = \Skif\Conf\ConfWrapper::value('bower_path');
?>
<script type="text/javascript" src="<?php echo $bower_path; ?>/bootstrap-multiselect/dist/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="<?php echo $bower_path; ?>/bootstrap-multiselect/dist/css/bootstrap-multiselect.css" type="text/css"/>

<select id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>[]" multiple="multiple" class="form-control">
    <?php
    foreach ($values_arr as $value => $title) {
        ?>
        <option value="<?php echo $value; ?>"<?php echo (in_array($value, $field_value) ? ' selected' : ''); ?>><?php echo $title; ?></option>
        <?php
    }
    ?>
</select>

<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#<?php echo $field_name; ?>').multiselect({
            nSelectedText: 'selected',
            nonSelectedText: 'Не выбрано'
        });
    })
</script>

