<!DOCTYPE html>
<html>
<head>
    <title>Ошибка 404 &mdash; документ не найден!</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab&subset=latin,cyrillic' rel='stylesheet'
          type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body { font-family: 'Roboto Slab', serif; margin: 0; padding: 0 10px;;  color: #494949; }
        a img { border: 0 }
        #body { width: 100%; max-width: 450px; margin: 0 auto; }
        h1 {font-size: 120px; margin: 0; text-align: center; font-weight: 400;     margin-bottom: 30px;}
        h1 div { font-size: 36px; }
        p { font-family: 'Roboto', serif; font-size: 14px; padding-bottom: 8px; }
        .narrow { width: 200px; margin: 0 auto;  }
        .list_title { margin-bottom: 10px; }
        a {color:  #449ddd;}
        #footer { margin-top: 30px; text-align: center; }
        #counters { text-align: center; margin-top: 40px; }
        .inline_block{display: inline-block}
        @media (max-width: 500px) {
            h1 {font-size: 100px;}
            h1 div { font-size: 26px; }
        }
    </style>
</head>
<body>

<div id="body">
    <h1>404
        <div>Документ не найден</div>
    </h1>
    <p class="narrow list_title">Возможные причины:</p>
    <p class="narrow">неправильно набран адрес;</p>
    <p class="narrow">документ был удален;</p>
    <p class="narrow">документ был перемещен;</p>
    <p class="narrow">документ был переименован.</p>
    <p>
        <?php
        $site_name = \Skif\Conf\ConfWrapper::value('site_name');
        $site_url = \Skif\Conf\ConfWrapper::value('site_url');
        $site_email = \Skif\Conf\ConfWrapper::value('site_url');
        ?>

        Зайдите с <a href="<?php echo \Skif\Utils::appendHttp($site_url); ?>">главной страницы</a>
        <span class="inline_block">или напишите <a href="mailto:<?php echo $site_email; ?>" title="написать администратору">администратору</a>.</span>
    </p>

    <p id="footer">&copy;&nbsp; <?php echo $site_name; ?></p>
</div>
</body>
</html>