<?php

namespace Skif\Conf;


class DefaultConf
{

    public static function get()
    {
        $conf = array();

        $conf['layout'] = array(
            'admin' => 'views/layouts/layout.admin.tpl.php',
            'empty' => 'views/layouts/layout.empty.tpl.php',
            'main' => 'views/layouts/layout.main.tpl.php'
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