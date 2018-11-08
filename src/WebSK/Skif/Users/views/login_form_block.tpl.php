<?php
/**
 * @var string $destination
 */

use Skif\Users\AuthController;

if (!isset($destination)) {
    $destination = '/';
}
?>

<form action="<?php echo AuthController::getLoginUrl(); ?>" class="form-inline" method="post">
    <div class="form-group">
        <label class="sr-only">Email</label>
        <div class="input-group">
            <span class="input-group-addon">@</span>
            <input type="text" name="email" placeholder="Email" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="sr-only">Пароль</label>
        <input type="password" name="password" placeholder="Пароль" class="form-control">
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="save_auth" value="1"> Запомнить
        </label>
    </div>
    <input type="hidden" name="destination" value="<?php echo $destination; ?>">
    <button type="submit" class="btn btn-default">Войти</button>
</form>
