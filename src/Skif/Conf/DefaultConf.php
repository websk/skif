<?php

namespace Skif\Conf;


class DefaultConf
{

    public static function get()
    {
        $conf = array();

        $conf['layout'] = array(
            'admin' => 'templates/layouts/layout.admin.tpl.php',
            'empty' => 'templates/layouts/layout.empty.tpl.php',
            'main' => 'templates/layouts/layout.main.tpl.php'
        );

        $conf['admin_menu'] = array(
            '/admin/content/page' => 'Страницы',
            '/admin/site_menu' => 'Менеджер меню',
            '/admin/content/news' => 'Новости',
            '/admin/users' => 'Пользователи',
            '/admin/blocks' => 'Блоки',
        );

        return $conf;
    }
}