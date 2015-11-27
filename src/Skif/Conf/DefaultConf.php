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
            array('link' => '/admin/content/page', 'name' => 'Страницы', 'icon' => '<i class="fa fa-files-o fa-fw"></i>'),
            array('link' => '/admin/site_menu', 'name' => 'Менеджер меню', 'icon' => '<i class="fa fa-bars fa-fw"></i>'),
            array('link' => '/admin/content/news', 'name' => 'Новости', 'icon' => '<i class="fa fa-newspaper-o fa-fw"></i>'),
            array('link' => '/admin/comments', 'name' => 'Комментарии', 'icon' => '<i class="fa fa-comments-o fa-fw"></i>'),
            array('link' => '/admin/users', 'name' => 'Пользователи', 'icon' => '<i class="fa fa-users fa-fw"></i>'),
            array('link' => '/admin/blocks', 'name' => 'Блоки', 'icon' => '<i class="fa fa-table fa-fw"></i>'),
        );

        $conf['skif_path'] = '/vendor/websk/skif';
        $conf['bower_path'] = '/vendor/bower';

        return $conf;
    }
}