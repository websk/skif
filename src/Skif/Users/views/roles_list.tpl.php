<?php
/**
 *
 */
?>
<p><a href="/admin/users/roles/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить роль</a></p>
<p></p>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-8">
            <col class="col-md-3">
            <col class="col-md-1">
        </colgroup>
    <?php
    $roles_ids_arr = \Skif\Users\UsersUtils::getRolesIdsArr();
    foreach ($roles_ids_arr as $role_id) {
        $role_obj = \Skif\Users\Role::factory($role_id);
        ?>
        <tr>
            <td>
                <a href="/admin/users/roles/edit/<?php echo $role_id; ?>"><?php echo $role_obj->getName(); ?></a>
            </td>
            <td>
                <?php echo $role_obj->getDesignation(); ?>
            </td>
            <td align="right">
                <a href="/admin/users/roles/edit/<?php echo $role_id; ?>"><span class="glyphicon glyphicon-edit" title="Редактировать"></span></a>
                <a href="/admin/users/roles/delete/<?php echo $role_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')"><span class="glyphicon glyphicon-remove" title="Удалить"></span></a>
            </td>
        </tr>
    <?
    }
    ?>
</table>
