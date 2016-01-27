<?php
/**
 * @var $destination
 */
if (!isset($destination)) {
    $destination = '/';
}
?>

<form action="/user/login" method="post">
    <div class="form-group">
        <label>Email</label>
        <div class="input-group">
            <span class="input-group-addon">@</span>
            <input type="text" name="email" maxlength="30" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label >Пароль</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="save_auth" value="1"> Запомнить
        </label>
    </div>
    <input type="hidden" name="destination" value="<?php echo $destination; ?>">
    <input type="submit" value="Войти" class="btn btn-default">
</form>
