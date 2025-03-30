<?php
/**
 * @var string $url
 * @var null|User $current_user_obj
 * @var bool $no_add_comments_for_unregistered_users
 */

use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\User;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Skif\Comment\RequestHandlers\CommentCreateHandler;
use WebSK\Slim\Router;

if ($no_add_comments_for_unregistered_users) {
    ?>
    <div>
        Неавторизованные пользователи не могут оставлять комментарии.
        Пожалуйста <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM); ?>">войдит на сайт</a> или <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM); ?>">зарегистрируйтесь</a>.
    </div>
<?php
    return;
}

?>
<form method="post" action="<?php echo Router::urlFor(CommentCreateHandler::class); ?>" id="comment_form" class="form-horizontal">
    <div class="form-group">
        <div class="col-md-12">
            <label for="comment">Сообщение</label>
            <textarea name="comment" id="comment" class="form-control" required></textarea>
        </div>
    </div>
    <?php
    if (!$current_user_obj) {
        ?>
        <div class="form-group">
            <label class="col-md-2">Имя</label>
            <div class="col-md-10">
                <input type="text" size="45" name="user_name" value="" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2">E-mail</label>
            <div class="col-md-10">
                <input type="text" size="45" name="user_mail" value="" class="form-control">
            </div>
        </div>
        <?php
        ?>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <img src="<?php echo Router::urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE); ?>" border="0" alt="Введите этот защитный код">
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