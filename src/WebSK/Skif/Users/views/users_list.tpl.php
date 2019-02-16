<?php
/**
 *
 */

use WebSK\Skif\Image\ImageManager;
use WebSK\Logger\LoggerRender;
use WebSK\Slim\Request;
use WebSK\Slim\Router;
use WebSK\Skif\Users\UsersRoutes;
use WebSK\Skif\Users\UsersUtils;

$requested_role_id = Request::getQueryParam('role_id', 0);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <form action="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST); ?>" class="form-inline">
                    <div class="form-group">
                        <label>Роль</label>

                        <select name="role_id" class="form-control">
                            <option value="0">Все</option>
                            <?php
                            $roles_ids_arr = UsersUtils::getRolesIdsArr();
                            foreach ($roles_ids_arr as $role_id) {
                                $role_obj = UsersUtils::loadRole($role_id);
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
                <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_LIST); ?>" class="btn btn-outline btn-info">
                    <span class="glyphicon glyphicon-wrench"></span> Редактировать роли</a>
            </div>
        </div>
    </div>
</div>

<p class="padding_top_10 padding_bottom_10">
    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_CREATE); ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить
        пользователя</a>
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
            $user_obj = UsersUtils::loadUser($user_id);
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
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]); ?>"><?php echo $user_obj->getName(); ?></a>
                </td>
                <td class="hidden-xs hidden-sm"><?php echo $user_obj->getEmail(); ?></td>
                <td align="right">
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]); ?>" title="Редактировать"
                       class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="<?php echo LoggerRender::getLoggerLinkForEntityObj($user_obj); ?>" target="_blank" title="Журнал"
                       class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-history fa-lg fa-fw"></span>
                    </a>
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_USER_DELETE, ['user_id' => $user_id], ['destination' => Request::getUri()->getPath()]); ?>"
                       onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить"
                       class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
