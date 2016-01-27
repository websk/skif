<?php
$destination = '/';
?>
<form action="/user/save/new" autocomplete="off" method="post" class="form-horizontal">
    <div xmlns="http://www.w3.org/1999/html">
        <div class="form-group">
            <label class="col-md-4 control-label">Фамилия Имя Отчество*</label>

            <div class="col-md-8">
                <input type="text" name="name" value="<?= $user_obj->getName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">E-mail</label>

            <div class="col-md-8">
                <input type="text" name="email" value="<?= $user_obj->getEmail() ?>" class="form-control">
            </div>
        </div>
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
            <label class="col-md-4 control-label">Адрес:</label>

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

        <div>
            <div class="form-group">
                <label class="col-md-4 control-label">Пароль</label>

                <div class="col-md-8"><input type="password" name="new_password_first" class="form-control"></div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Подтверждение пароля</label>

                <div class="col-md-8"><input type="password" name="new_password_second" class="form-control"></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <div class="help-block">Поля помеченные * обязательны для заполнения</div>
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>


