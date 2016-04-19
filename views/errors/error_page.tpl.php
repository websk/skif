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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ошибка <?php echo $error_code; ?> &mdash; <?php echo $error_messages_arr[$error_code]['title']; ?>!</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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