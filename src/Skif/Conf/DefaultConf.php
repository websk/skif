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
            'main' => 'layouts/layout.main.tpl.php'
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