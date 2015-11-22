<?php
$key_value_ids_arr = \Skif\KeyValue\KeyValueUtils::getKeyValueIdsArr();
?>
<p><a href="/admin/key_value/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить переменную</a></p>
<p></p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-4 col-sm-6 col-xs-6">
            <col class="col-md-4 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
    <?php
    foreach ($key_value_ids_arr as $key_value_id) {
        $key_value_obj = \Skif\KeyValue\KeyValue::factory($key_value_id);
        ?>
        <tr>
            <td><?php echo $key_value_obj->getId(); ?></td>
            <td>
                <a href="/admin/key_value/edit/<?php echo $key_value_id ?>"><?= $key_value_obj->getName() ?></a>
            </td>
            <td class="hidden-sm hidden-xs">
                <?php echo $key_value_obj->getDescription(); ?>
            </td>
            <td align="right">
                <a href="/admin/key_value/edit/<?php echo $key_value_id; ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                    <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                </a>
                <a href="/admin/key_value/delete/<?php echo $key_value_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
                    <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
    </table>
