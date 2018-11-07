<?php
/**
 *
 */

use Skif\Image\ImageManager;
use Skif\Logger\LoggerUtils;
use WebSK\Skif\Users\Role;
use WebSK\Skif\Users\User;
use WebSK\Skif\Users\UsersUtils;

$requested_role_id = array_key_exists('role_id', $_REQUEST) ? $_REQUEST['role_id'] : 0;
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <form action="/admin/users" class="form-inline">
                    <div class="form-group">
                        <label>Роль</label>

                        <select name="role_id" class="form-control">
                            <option value="0">Все</option>
                            <?php
                            $roles_ids_arr = UsersUtils::getRolesIdsArr();
                            foreach ($roles_ids_arr as $role_id) {
                                $role_obj = Role::factory($role_id);
                                echo '<option value="' . $role_id . '" ' . ($role_id == $requested_role_id ? 'selected' : '') . '>' . $role_obj->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Выбрать" class="btn btn-default">
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <a href="/admin/users/roles" class="btn btn-outline btn-info">
                    <span class="glyphicon glyphicon-wrench"></span> Редактировать роли</a>
            </div>
        </div>
    </div>
</div>

<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/users/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить пользователя</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-1 hidden-sm hidden-xs">
            <col class="col-md-4 col-sm-6 col-xs-6">
            <col class="col-md-3 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <?php
        $users_ids_arr = UsersUtils::getUsersIdsArr($requested_role_id);
        foreach ($users_ids_arr as $user_id) {
            $user_obj = User::factory($user_id);
            ?>
            <tr>
                <td><?php echo $user_obj->getId(); ?></td>
                <td class="hidden-xs hidden-sm">
                    <?php
                    if ($user_obj->getPhoto()) {
                        echo '<img src="' . ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '30_30') . '" class="img-thumbnail">';
                    }
                    ?>
                </td>
                <td>
                    <a href="/admin/users/edit/<?php echo $user_id; ?>"><?php echo $user_obj->getName(); ?></a>
                </td>
                <td class="hidden-xs hidden-sm"><?php echo $user_obj->getEmail(); ?></td>
                <td align="right">
                    <a href="/admin/users/edit/<?php echo $user_id; ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="<?php echo LoggerUtils::getLoggerUrlByObject($user_obj); ?>" target="_blank" title="Журнал" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-history fa-lg fa-fw"></span>
                    </a>
                    <a href="/user/delete/<?php echo $user_id; ?>?destination=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                    </a>
                </td>
            </tr>
            <?
        }
        ?>
    </table>
