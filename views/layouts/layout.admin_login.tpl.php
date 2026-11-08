<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\AuthRoutes;
use WebSK\Skif\SkifApp;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Router;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;
use WebSK\Views\LayoutDTO;
?>
<!doctype html>
<html lang="ru" data-lte-color-mode="off">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></title>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/css/AdminLTE.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/crud/crud.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/all.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/v4-shims.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet">
</head>
<body class="login-page bg-body-secondary">
<div class="login-box">
    <div class="login-logo mb-3">
        <a href="/" class="text-decoration-none">
            <img src="<?php echo SkifPath::wrapAssetsVersion('images/admin/skif_small_logo.png'); ?>"
                 alt="СКИФ" height="39" title="Система управления сайтом СКИФ / websk.ru">
        </a>
    </div>
    <div class="card card-primary card-outline shadow">
        <div class="card-body login-card-body">
            <h1 class="login-box-msg h5">Вход в систему управления</h1>
            <?php echo Messages::renderMessages(); ?>
            <form action="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN); ?>" method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text" aria-hidden="true">@</span>
                    <label for="login-email" class="visually-hidden">Email</label>
                    <input id="login-email" type="email" name="email" maxlength="254" placeholder="Email"
                           class="form-control" autocomplete="username" required>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-key" aria-hidden="true"></i></span>
                    <label for="login-password" class="visually-hidden">Пароль</label>
                    <input id="login-password" class="form-control" placeholder="Пароль" name="password"
                           type="password" autocomplete="current-password" required>
                </div>
                <div class="form-check mb-3">
                    <input id="save-auth" class="form-check-input" name="save_auth" type="checkbox" value="1">
                    <label for="save-auth" class="form-check-label">Запомнить меня</label>
                </div>
                <input type="hidden" name="destination" value="<?php echo Router::urlFor(SkifApp::ROUTE_NAME_ADMIN); ?>">
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/js/adminlte.min.js'); ?>"></script>
</body>
</html>
