<?php
/**
 *
 */

use WebSK\Auth\AuthRoutes;
use WebSK\Skif\SkifApp;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Router;
use WebSK\Utils\Messages;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>СКИФ - Система управления сайтом</title>

    <link href="<?php echo SkifPath::wrapUrlPath('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <!-- jQuery -->
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery/jquery.min.js'); ?>"></script>

    <!-- Bootstrap -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.min.js'); ?>"></script>

    <!-- AdminLTE -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/AdminLTE/css/AdminLTE.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/AdminLTE/css/skin-blue.min.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet" type="text/css">
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <div><img src="<?php echo SkifPath::wrapAssetsVersion('images/admin/skif_small_logo.png'); ?>" alt="СКИФ" border="0" height="39" title="Система управления сайтом СКИФ / websk.ru"></div>
                    <h3 class="panel-title">Вход в систему управления</h3>
                </div>
                <div class="panel-body">
                    <?php
                    echo Messages::renderMessages();
                    ?>

                    <form action="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN); ?>" method="post">
                        <div class="form-group">
                            <label class="sr-only">Email</label>
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="text" name="email" maxlength="30" placeholder="Email" class="form-control">
                            </div>
                        </div>
                            <div class="form-group">
                                <label class="sr-only">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-key"></span></span>
                                    <input class="form-control" placeholder="Пароль" name="password" type="password" value="">
                                </div>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="save_auth" type="checkbox" value="Запомнить меня">Запомнить меня
                                </label>
                            </div>
                            <input type="hidden" name="destination" value="<?php echo Router::pathFor(SkifApp::ROUTE_NAME_ADMIN); ?>">
                            <input type="submit" value="Войти" class="btn btn-lg btn-primary btn-block">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

