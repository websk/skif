<?php
require_once __DIR__ . '/../vendor/autoload.php';


$user_obj = new \Skif\Users\User();

$user_obj->setName('Администратор');
$user_obj->setEmail(\Skif\Conf\ConfWrapper::value('site_email'));
$user_obj->setPassw(\Skif\Users\AuthUtils::getHash('12345'));
$user_obj->setConfirm(1);
$user_obj->save();

$user_id = $user_obj->getId();


$query = "INSERT INTO users_roles SET user_id=?, role_id=1";
\Skif\DB\DBWrapper::query($query,
    array(
        $user_id
    )
);


$content_obj = new \Skif\Content\Content();
$content_obj->setTitle('Главная');
$content_obj->setAnnotation('');
$content_obj->setBody('');
$content_obj->setType('page');
$content_obj->setCreatedAt(date('Y-m-d H:i:s'));
$content_obj->setDescription('');
$content_obj->setKeywords('');
$content_obj->setTemplateId(1);
$content_obj->setLastModifiedAt(date('Y-m-d H:i:s'));
$content_obj->setUrl('/');
$content_obj->setIsPublished(1);
$content_obj->save();


echo 'ОК';