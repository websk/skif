<?php
?>

<form action="/user/login" method="post">
    <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" class="form-control">
    </div>
    <div class="form-group">
        <label >Пароль</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="save_auth" value="1"> Запомнить меня
        </label>
    </div>
    <input type="hidden" name="destination" value="/">
    <input type="submit" value="Войти" class="btn btn-default">
</form>
