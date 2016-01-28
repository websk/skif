<?php
?>

<form action="<?php echo \Skif\Users\UserController::getConfirmUrl(); ?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label class="col-md-2 control-label">Email</label>
        <div class="col-md-10">
            <input type="text" name="email" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <img src="<?php echo \Skif\Captcha\Captcha::getUrl(); ?>" border="0" alt="Введите этот защитный код">
            <input type="text" size="5" name="captcha" class="form-control">
            <span class="help-block">Введите код, изображенный на картинке</span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-8">
            <button type="submit" class="btn btn-primary">Получить ссылку</button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-8">
            <a href="<?php echo \Skif\Users\UserController::getRegistrationFormUrl(); ?>">Регистрация</a>
        </div>
    </div>
</form>

