<?php
/**
 *
 */

use Skif\Users\Role;
use Skif\Users\UsersUtils;

?>
<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/users/roles/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить роль</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-6 col-sm-6 col-xs-6">
            <col class="col-md-3 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
    <?php
    $roles_ids_arr = UsersUtils::getRolesIdsArr();
    foreach ($roles_ids_arr as $role_id) {
        $role_obj = Role::factory($role_id);
        ?>
        <tr>
            <td><?php echo $role_obj->getId(); ?></td>
            <td>
                <a href="/admin/users/roles/edit/<?php echo $role_id; ?>"><?php echo $role_obj->getName(); ?></a>
            </td>
            <td class="hidden-sm hidden-xs">
                <?php echo $role_obj->getDesignation(); ?>
            </td>
            <td align="right">
                <a href="/admin/users/roles/edit/<?php echo $role_id; ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                    <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                </a>
                <a href="/admin/users/roles/delete/<?php echo $role_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')"  title="Удалить" class="btn btn-outline btn-default btn-sm">
                    <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                </a>
            </td>
        </tr>
    <?
    }
    ?>
</table>
