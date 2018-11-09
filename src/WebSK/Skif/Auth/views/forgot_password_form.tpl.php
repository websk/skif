<?php

use WebSK\Skif\Captcha\CaptchaRoutes;
use WebSK\Skif\Router;
use WebSK\Skif\Auth\AuthRoutes;

?>
<form action="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_FORGOT_PASSWORD); ?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label class="col-md-2 control-label">Email</label>
        <div class="col-md-10">
            <input type="text" name="email" class="form-control">
            <span class="help-block">Введите адрес электронной почты, который вы указывали при регистрации</span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <img src="<?php echo Router::pathFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE); ?>" border="0" alt="Введите этот защитный код">
            <input type="text" size="5" name="captcha" class="form-control">
            <span class="help-block">Введите код, изображенный на картинке</span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Восстановить пароль</button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <a href="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM); ?>">Регистрация</a>
        </div>
    </div>
</form>


