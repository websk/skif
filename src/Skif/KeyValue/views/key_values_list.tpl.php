<?php
$key_value_ids_arr = \Skif\KeyValue\KeyValueUtils::getKeyValueIdsArr();
?>
<p><a href="/admin/key_value/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить переменную</a></p>
<p></p>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-4">
            <col class="col-md-6">
            <col class="col-md-2">
        </colgroup>
    <?php
    foreach ($key_value_ids_arr as $key_value_id) {
        $key_value_obj = \Skif\KeyValue\KeyValue::factory($key_value_id);
        ?>
        <tr>
            <td>
                <a href="/admin/key_value/edit/<?php echo $key_value_id ?>"><?= $key_value_obj->getName() ?></a>
            </td>
            <td>
                <?= $key_value_obj->getDescription() ?>
            </td>
            <td align="right">
                <a href="/admin/key_value/edit/<?php echo $key_value_id; ?>"><span class="glyphicon glyphicon-edit text-warning" title="Редактировать"></span></a>
                <a href="/admin/key_value/delete/<?php echo $key_value_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')"><span class="glyphicon glyphicon-remove text-danger" title="Удалить"></span></a>
            </td>
        </tr>
        <?php
    }
    ?>
    </table>
