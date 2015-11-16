<?php
/**
 *
 */

$requested_role_id = array_key_exists('role_id', $_REQUEST) ? $_REQUEST['role_id'] : 0;
?>
<div class="jumbotron">
    <div class="row">
        <div class="col-md-8">
            <form action="/admin/users" class="form-inline">
                <div class="form-group">
                    <label>Роль</label>

                    <select name="role_id" class="form-control">
                        <option value="0">Все</option>
                        <?php
                        $roles_ids_arr = \Skif\Users\UsersUtils::getRolesIdsArr();
                        foreach ($roles_ids_arr as $role_id) {
                            $role_obj = \Skif\Users\Role::factory($role_id);
                            echo '<option value="' . $role_id . '" ' . ($role_id == $requested_role_id ? 'selected' : '') . '>' . $role_obj->getName() . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" value="Выбрать" class="btn btn-default">
            </form>
        </div>
        <div class="col-md-4"><a href="/admin/users/roles" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span> Редактировать роли</a></div>
    </div>

</div>

<p></p>

<p><a href="/admin/users/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить пользователя</a></p>
<p></p>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1">
            <col class="col-md-8">
            <col class="col-md-2">
            <col class="col-md-1">
        </colgroup>
    <?php
    $users_ids_arr = \Skif\Users\UsersUtils::getUsersIdsArr($requested_role_id);
    foreach ($users_ids_arr as $user_id) {
        $user_obj = \Skif\Users\User::factory($user_id);
        ?>
        <tr>
            <td>
                <?php
                if ($user_obj->getPhoto()) {
                    echo '<img src="' . \Skif\Image\ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '30_30') . '" class="img-thumbnail">';
                }
                ?>
            </td>
            <td>
                <a href="/admin/users/edit/<?php echo $user_id; ?>"><?php echo $user_obj->getName(); ?></a>
            </td>
            <td><?php echo $user_obj->getEmail(); ?></td>
            <td align="right">
                <a href="/admin/users/edit/<?php echo $user_id; ?>"><span class="glyphicon glyphicon-edit text-warning" title="Редактировать"></span></a>
                <a href="/user/delete/<?php echo $user_id; ?>?destination=/admin/users" onClick="return confirm('Вы уверены, что хотите удалить?')"><span class="glyphicon glyphicon-remove text-danger" title="Удалить"></span></a>
            </td>
        </tr>
    <?
    }
    ?>
</table>
