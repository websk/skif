<?php
/**
 *
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>СКИФ - Система управления сайтом</title>

    <!-- Bootstrap Core CSS -->
    <link href="/vendor/bower/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/vendor/bower/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Custom CSS -->
    <link href="/vendor/websk/skif/assets/libraries/sb-admin-2/css/sb-admin-2.css" rel="stylesheet">
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Вход в систему управления</h3>
                </div>
                <div class="panel-body">
                    <form action="/user/login" method="post">
                        <div class="form-group">
                            <label class="sr-only">Email</label>
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="text" name="email" maxlength="30" placeholder="Email" class="form-control">
                            </div>
                        </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Пароль" name="password" type="password" value="">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="save_auth" type="checkbox" value="Запомнить меня">Запомнить меня
                                </label>
                            </div>
                            <input type="hidden" name="destination" value="/admin">
                            <input type="submit" value="Войти" class="btn btn-lg btn-primary btn-block">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>

