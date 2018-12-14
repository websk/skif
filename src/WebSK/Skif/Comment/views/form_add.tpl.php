<?php
/**
 * @var $url
 */

use WebSK\Skif\Auth\AuthRoutes;
use WebSK\Skif\Captcha\CaptchaRoutes;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Router;
use WebSK\Skif\Auth\Auth;

$user_name = '';
$user_email = '';

$current_user_id = Auth::getCurrentUserId();

if (ConfWrapper::value('comments.no_add_comments_for_unregistered_users')) {
    ?>
    <div>
        Неавторизованные пользователи не могут оставлять комментарии.
        Пожалуйста <a href="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM); ?>">войдит на сайт</a> или <a href="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM); ?>">зарегистрируйтесь</a>.
    </div>
<?php
    return;
}

?>
<form method="post" action="/comments/add" id="comment_form" class="form-horizontal">
    <div class="form-group">
        <div class="col-md-12">
            <label for="comment">Сообщение</label>
            <textarea name="comment" id="comment" class="form-control"></textarea>
        </div>
    </div>
    <?php
    if (!Auth::getCurrentUserId()) {
        ?>
        <div class="form-group">
            <label class="col-md-2">Имя</label>
            <div class="col-md-10">
                <input type="text" size="45" name="user_name" value="<?php echo $user_name ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2">E-mail</label>
            <div class="col-md-10">
                <input type="text" size="45" name="user_mail" value="<?php echo $user_email ?>" class="form-control">
            </div>
        </div>
        <?php
        ?>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <img src="<?php echo Router::pathFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE); ?>" border="0" alt="Введите этот защитный код">
                    <input type="text" size="5" name="captcha" class="form-control">
                    <span class="help-block">Введите код, изображенный на картинке</span>
                </div>
            </div>
        <?php
        ?>
    <?php
    }
    ?>
    <div class="form-group">
        <div class="col-md-12">
            <input type="hidden" name="url" value="<?php echo $url ?>">
            <input type="submit" value="Отправить сообщение" class="btn btn-primary">
        </div>
    </div>
</form>
<p></p>