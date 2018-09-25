<?php

namespace Skif\Conf;


class DefaultConf
{

    public static function get()
    {
        $conf = array();

        $conf['layout'] = array(
            'admin' => 'layouts/layout.admin.tpl.php',
            'empty' => 'layouts/layout.empty.tpl.php',
            'blank' => 'layouts/layout.blank.tpl.php',
            'main' => 'layouts/layout.main.tpl.php'
        );


        $conf['admin_menu'] = array(
            array('link' => '/admin/site_menu', 'name' => 'Менеджер меню', 'icon' => '<i class="fa fa-bars fa-fw"></i>'),
            array('link' => '/admin/blocks', 'name' => 'Блоки', 'icon' => '<i class="fa fa-table fa-fw"></i>'),
            array('link' => '/admin/poll', 'name' => 'Опросы', 'icon' => '<i class="fa fa-bar-chart fa-fw"></i>'),
            array('link' => '/admin/form', 'name' => 'Формы', 'icon' => '<i class="fa fa-list-alt fa-fw"></i>'),
            array('link' => '/admin/comments', 'name' => 'Комментарии', 'icon' => '<i class="fa fa-comments-o fa-fw"></i>'),
            array('link' => '/admin/users', 'name' => 'Пользователи', 'icon' => '<i class="fa fa-users fa-fw"></i>'),
            array('link' => '/admin/rating', 'name' => 'Рейтинги', 'icon' => '<i class="fa fa-star-o fa-fw"></i>'),
            array(
                'link' => '#',
                'name' => 'Настройки<span class="fa arrow"></span>',
                'icon' => '<i class="fa fa-wrench fa-fw"></i>',
                'sub_menu' => array(
                    array('link' => \Skif\CRUD\CRUDController::getListUrl('\Skif\Content\ContentType'), 'name' => 'Типы контента'),
                    array('link' => \Skif\CRUD\CRUDController::getListUrl('\Skif\Content\Template'), 'name' => 'Темы'),
                    array('link' => '/admin/redirect', 'name' => 'Редиректы'),
                    array('link' => '/admin/key_value', 'name' => 'Параметры'),
                )
            ),
        );

        $conf['skif_url_path'] = '/admin';
        $conf['skif_assets_version'] = 1;

        $conf['assets_url_path'] = '/assets';

        return $conf;
    }
}