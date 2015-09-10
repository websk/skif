<?php
/**
 * @var $key_value_id
 */

if ($key_value_id != 'new') {
    $key_value_obj = \Skif\KeyValue\KeyValue::factory($key_value_id);
    ?>
    <script>
        function deleteComfirm() {
            if (confirm("Вы действительно хотите удалить <?php echo $key_value_obj->getName(); ?>?")) {
                $('#DelForm').submit();
            }
        }
    </script>

    <form id="DelForm" action="/admin/key_value/delete/<?php echo $key_value_id; ?>" method="post">
    </form>
    <?php
} else {
    $key_value_obj = new \Skif\KeyValue\KeyValue();
}
?>

<form role="form" action="/admin/key_value/save/<?php echo $key_value_id; ?>" method="post">
    <div class="form-group">
        <label>Название</label>
        <input class="form-control" type="text" value="<?php echo $key_value_obj->getName(); ?>"
               name="name"<?php echo ($key_value_id != 'new') ? ' disabled' : '' ?>>
    </div>
    <div class="form-group">
        <label>Описание</label>
        <input class="form-control" type="text" value="<?php echo $key_value_obj->getDescription(); ?>" name="description">
    </div>
    <div class="form-group">
        <label>Значение</label>
        <textarea class="form-control" rows="10" name="value"><?php echo $key_value_obj->getValue(); ?></textarea>
    </div>
    <div class="form-group" align="left">
        <input class="btn btn-primary" type="submit" name="yt0" value="Сохранить">
        <?php
        if ($key_value_id != 'new') {
            ?>
            <a href="#" class="btn btn-danger pull-right" onclick="deleteComfirm(); return false;">Удалить</a>
            <?php
        }
        ?>
    </div>
</form>
