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
use WebSK\Skif\Router;
use WebSK\Utils\Sanitize;

if ($no_add_comments_for_unregistered_users) {
    ?>
    <div class="alert alert-info" role="status">
        Неавторизованные пользователи не могут оставлять комментарии.
        Пожалуйста <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM); ?>">войдит на сайт</a> или <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM); ?>">зарегистрируйтесь</a>.
    </div>
<?php
    return;
}

?>
<form method="post" action="<?php echo Router::urlFor(CommentCreateHandler::class); ?>" id="comment_form" class="card card-primary card-outline mb-3">
    <div class="card-body">
        <div class="mb-3">
            <label for="comment" class="form-label">Сообщение</label>
            <textarea name="comment" id="comment" class="form-control" required></textarea>
        </div>
        <?php
        if (!$current_user_obj) {
            ?>
            <div class="row mb-3">
                <label for="comment-user-name" class="col-md-2 col-form-label">Имя</label>
                <div class="col-md-10">
                    <input id="comment-user-name" type="text" name="user_name" class="form-control" autocomplete="name" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="comment-user-email" class="col-md-2 col-form-label">E-mail</label>
                <div class="col-md-10">
                    <input id="comment-user-email" type="email" name="user_mail" class="form-control" autocomplete="email">
                </div>
            </div>
            <div class="row mb-3">
                <div class="offset-md-2 col-md-10">
                    <img src="<?php echo Router::urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE); ?>" alt="Защитный код" class="d-block mb-2">
                    <label for="comment-captcha" class="form-label">Введите код, изображённый на картинке</label>
                    <input id="comment-captcha" type="text" name="captcha" class="form-control" autocomplete="off" required>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="card-footer bg-transparent">
        <input type="hidden" name="url" value="<?php echo Sanitize::sanitizeAttrValue($url); ?>">
        <input type="submit" value="Отправить сообщение" class="btn btn-primary">
    </div>
</form>
