<?php
$destination = '/';

$user_obj = new \Skif\Users\User();
?>
<form action="/user/save/new" autocomplete="off" method="post" class="form-horizontal">
    <div xmlns="http://www.w3.org/1999/html">
        <div class="form-group">
            <label class="col-md-4 control-label">Имя</label>

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
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>


