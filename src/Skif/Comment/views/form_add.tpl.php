<?php
/**
 * @var $url
 */

$user_name = '';
$user_email = '';
?>
<p class="text-right"><a href="/mail.php" class="btn btn-default">Связаться по электронной почте</a></p>
<?php
if (!\Skif\Users\AuthUtils::getCurrentUserId()) {
    ?>
    <p></p>
    <p class="alert alert-warning">Уважаемые читатели! Для оперативного ответа на ваши вопросы, и помощи в решении возникших проблем,
        пожалуйста, укажите ваши ФИО полностью, а также желательно адрес электронной почты. Не указывайте ваш номер
        читательского билета.
    </p>
<?php
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
    if (!\Skif\Users\AuthUtils::getCurrentUserId()) {
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
                    <img src="/captcha/generate" border="0" alt="Введите этот защитный код">
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