<?php
/**
 * @var User $user_obj
 * @var array $user_roles_ids_arr
 * @var string $save_handler_url
 */

use WebSK\Skif\Image\ImageManager;
use WebSK\Slim\Router;
use WebSK\Skif\Auth\Auth;
use WebSK\Skif\Users\User;
use WebSK\Skif\Users\UsersRoutes;
use WebSK\Skif\Users\UsersUtils;
use WebSK\Utils\Url;

$destination = Url::getUriNoQueryString();

?>
<form id="profile_form" action="<?php echo $save_handler_url; ?>" autocomplete="off" method="post"
      class="form-horizontal" enctype="multipart/form-data">
    <div xmlns="http://www.w3.org/1999/html">
        <div class="form-group has-warning">
            <label class="col-md-4 control-label">Имя на сайте</label>

            <div class="col-md-8">
                <input type="text" name="name" value="<?= $user_obj->getName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Имя</label>

            <div class="col-md-8">
                <input type="text" name="first_name" value="<?= $user_obj->getFirstName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Фамилия</label>

            <div class="col-md-8">
                <input type="text" name="last_name" value="<?= $user_obj->getLastName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group has-warning">
            <label class="col-md-4 control-label">E-mail</label>

            <div class="col-md-8">
                <input type="text" name="email" value="<?= $user_obj->getEmail() ?>" class="form-control">
            </div>
        </div>
        <?php
        if (Auth::currentUserIsAdmin()) {
            ?>
            <div class="form-group">
                <label class="col-md-4 control-label">Роль</label>

                <div class="col-md-8">
                    <div>
                        <?php
                        $roles_ids_arr = UsersUtils::getRolesIdsArr();
                        foreach ($roles_ids_arr as $role_id) {
                            $role_obj = UsersUtils::loadRole($role_id);
                            ?>
                            <div class="checkbox">
                                <label for="roles_<?php echo $role_id; ?>">
                                    <input value="<?php echo $role_id; ?>" id="roles_<?php echo $role_id; ?>"
                                           type="checkbox"
                                           name="roles[]"<?php echo(in_array($role_id, $user_roles_ids_arr) ? ' checked' : '') ?>>
                                    <?php echo $role_obj->getName(); ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-4 col-md-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="confirm"
                                   value="1"<?= $user_obj->isConfirm() ? ' checked' : '' ?>> Регистрация подтверждена
                        </label>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="form-group">
            <label class="col-md-4 control-label">Дата рождения</label>

            <div class="col-md-8">
                <input type="text" name="birthday" value="<?= $user_obj->getBirthDay() ?>" maxlength="10"
                       class="form-control">
                <span class="help-block">(дд.мм.гггг)</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Телефон</label>

            <div class="col-md-8">
                <input type="text" name="phone" value="<?= $user_obj->getPhone() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Город</label>

            <div class="col-md-8">
                <input type="text" name="city" value="<?= $user_obj->getCity() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Адрес</label>

            <div class="col-md-8">
                <input type="text" name="address" value="<?= $user_obj->getAddress() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Дополнительная информация</label>

            <div class="col-md-8">
                <textarea name="comment" rows="7" class="form-control"><?= $user_obj->getComment() ?></textarea>
            </div>
        </div>

        <?php
        if ($user_obj->getId()) {
            ?>
            <div class="form-group">
                <div class="col-md-offset-4 col-md-8">
                    <h3>Смена пароля</h3>

                    <div class="help-block">Заполняется, если Вы хотите изменить пароль</div>
                </div>
            </div>
            <?php
        }
        ?>
        <div>
            <div class="form-group">
                <label class="col-md-4 control-label">Пароль</label>

                <div class="col-md-8"><input type="password" name="new_password_first" class="form-control"></div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Подтверждение пароля</label>

                <div class="col-md-8"><input type="password" name="new_password_second" class="form-control"></div>
            </div>

            <?php
            if ($user_obj->getId() && Auth::currentUserIsAdmin()) {
                ?>
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-8">
                        <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_USER_CREATE_PASSWORD, ['user_id' => $user_obj->getId()], ['destination' => $destination]) ?>">Сгенерировать
                            пароль и выслать пользователю</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <h3>Фотография пользователя</h3>
            <?php
            if (!$user_obj->getPhoto()) {
                ?>
                <div class="form-group">
                    <input type="file" name="image_file" size="12">
                </div>
            <?php
            } else {
            ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("a#user_photo").fancybox({});
                    });
                </script>
                <a id="user_photo"
                   href="<?php echo ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '600_auto'); ?>">
                    <img
                        src="<?php echo ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '200_auto'); ?>"
                        border="0" class="img-responsive img-thumbnail">
                </a>

                <div>
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_USER_DELETE_PHOTO, ['user_id' => $user_obj->getId()], ['destination' => $destination]); ?>"
                       class="btn btn-default">Удалить фото</a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>

<script type="text/javascript">
    $().ready(function () {
        $.validator.setDefaults({
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $("#profile_form").validate({
            ignore: ":hidden",
            rules: {
                name: "required",
                email: "required"
            },
            messages: {
                name: "Это поле обязательно для заполнения",
                email: "Это поле обязательно для заполнения"
            }
        });
    })
</script>
