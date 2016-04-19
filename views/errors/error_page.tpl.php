<?php
/**
 * @var $error_code
 */

$error_messages_arr = array(
    404 => array(
        'title' => 'документ не найден',
        'messages' => array('неправильно набран адрес', 'документ был удален', 'документ был перемещен', 'документ был переименован')
    ),
    403 => array(
        'title' => 'доступ запрещен'
    ),
);

if (!array_key_exists($error_code, $error_messages_arr)) {
    return;
}

$bower_path = \Skif\Conf\ConfWrapper::value('bower_path');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ошибка <?php echo $error_code; ?> &mdash; <?php echo $error_messages_arr[$error_code]['title']; ?>!</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <link href="<?php echo $bower_path; ?>/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {margin: 0; padding: 0 10px;}
        #body { width: 100%; max-width: 450px; margin: 0 auto; }
        h1 {font-size: 120px; margin: 0; text-align: center; font-weight: 400;     margin-bottom: 30px;}
        h1 div { font-size: 36px; }
        p {padding-bottom: 8px; }
        .narrow { width: 200px; margin: 0 auto;  }
        .list_title { margin-bottom: 10px; }
        a {color:  #449ddd;}
        #footer { margin-top: 30px; text-align: center; }
        .inline_block{display: inline-block}
        @media (max-width: 500px) {
            h1 {font-size: 100px;}
            h1 div { font-size: 26px; }
        }
    </style>
</head>
<body>

<div id="body">
    <h1><?php echo $error_code; ?>
        <div><?php echo ucfirst($error_messages_arr[$error_code]['title']); ?></div>
    </h1>
    <?php
    if (array_key_exists('messages', $error_messages_arr[$error_code])) {
        ?>
        <p class="narrow list_title">Возможные причины:</p>
        <?php
        foreach ($error_messages_arr[$error_code]['messages'] as $message) {
            ?>
            <p class="narrow"><?php echo $message; ?>;</p>
        <?php
        }
    }
    ?>
    <p>
        <?php
        $site_name = \Skif\Conf\ConfWrapper::value('site_name');
        $site_url = \Skif\Conf\ConfWrapper::value('site_url');
        $site_email = \Skif\Conf\ConfWrapper::value('site_email');
        ?>

        Зайдите с <a href="<?php echo \Skif\Utils::appendHttp($site_url); ?>">главной страницы</a>
        <span class="inline_block">или напишите <a href="mailto:<?php echo $site_email; ?>" title="написать администратору">администратору</a>.</span>
    </p>

    <p id="footer">&copy;&nbsp; <?php echo $site_name; ?></p>
</div>
</body>
</html>