<?php
/**
 * @var int $user_id
 */

use Skif\Http;
use Skif\Image\ImageManager;
use Skif\UrlManager;
use Skif\Users\AuthUtils;
use Skif\Users\User;

if ($user_id == 'new') {
    $user_obj = new User();
} else {
    $user_obj = User::factory($user_id);
}

$current_user_id = AuthUtils::getCurrentUserId();
$current_user_obj = User::factory($current_user_id);

if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
    Http::exit403();
}

$destination = UrlManager::getUriNoQueryString();

?>
<form id="profile_form" action="/user/save/<?php echo $user_id; ?>" autocomplete="off" method="post"
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
        if (AuthUtils::currentUserIsAdmin()) {
            ?>
            <div class="form-group">
                <label class="col-md-4 control-label">Роль</label>

                <div class="col-md-8">
                    <div>
                        <?php
                        $roles_ids_arr = \Skif\Users\UsersUtils::getRolesIdsArr();
                        foreach ($roles_ids_arr as $role_id) {
                            $role_obj = \Skif\Users\Role::factory($role_id);
                            ?>
                            <div class="checkbox">
                                <label for="roles_<?php echo $role_id; ?>">
                                    <input value="<?php echo $role_id; ?>" id="roles_<?php echo $role_id; ?>"
                                           type="checkbox"
                                           name="roles[]"<?php echo(in_array($role_id, $user_obj->getRoleIdsArr()) ? ' checked' : '') ?>>
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
        if ($user_id != 'new') {
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
            if (($user_id != 'new') && AuthUtils::currentUserIsAdmin()) {
                ?>
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-8">
                        <a href="/user/create_password/<?= $user_id ?>?destination=<?php echo $destination; ?>">Сгенерировать
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
                    <a href="/user/delete_photo/<?php echo $user_id; ?>?destination=<?php echo $destination; ?>"
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
