<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Skif\Auth\Auth;
use Websk\Utils\Messages;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;

class SiteMenuController
{
    public function listItemsAdminAction($site_menu_id, $site_menu_item_id = 0)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $site_menu_obj = SiteMenu::factory($site_menu_id);

        $html = SkifPhpRender::renderTemplateBySkifModule(
            'SiteMenu',
            'admin_site_menu_items_list.tpl.php',
            array('site_menu_id' => $site_menu_id, 'parent_id' => $site_menu_item_id)
        );

        $breadcrumbs_arr = self::getBreadcrumbsArr($site_menu_id, $site_menu_item_id);

        echo SkifPhpRender::renderTemplate(
            ConfWrapper::value('layout.admin'),
            array(
                'title' => $site_menu_obj->getName(),
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public static function getBreadcrumbsArr($site_menu_id, $site_menu_item_parent_id = 0)
    {
        $site_menu_obj = SiteMenu::factory($site_menu_id);

        $breadcrumbs_arr = array(
            'Менеджер меню' => '/admin/site_menu',
            $site_menu_obj->getName() => '/admin/site_menu/' . $site_menu_id . '/items/list/0'
        );

        if (!$site_menu_item_parent_id) {
            return $breadcrumbs_arr;
        }

        $site_menu_parent_item_obj = SiteMenuItem::factory($site_menu_item_parent_id);

        $ancestors_ids_arr = $site_menu_parent_item_obj->getAncestorsIdsArr();
        $ancestors_ids_arr = array_reverse($ancestors_ids_arr);

        foreach ($ancestors_ids_arr as $item_parent_id) {
            $site_menu_item_obj = SiteMenuItem::factory($item_parent_id);

            $breadcrumbs_arr[$site_menu_item_obj->getName()] = '/admin/site_menu/' . $site_menu_id . '/items/list/' . $item_parent_id;
        }

        if ($site_menu_item_parent_id) {
            $breadcrumbs_arr[$site_menu_parent_item_obj->getName()] = '/admin/site_menu/' . $site_menu_id . '/items/list/' . $site_menu_item_parent_id;
        }

        return $breadcrumbs_arr;
    }

    public function listForMoveItemsAdminAction($site_menu_id, $site_menu_item_parent_id = 0)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $move_item_id = array_key_exists('move_item_id', $_REQUEST) ? $_REQUEST['move_item_id'] : null;

        if (!$move_item_id) {
            Messages::setError('Не выбран пункт меню');
            Redirects::redirect('/admin/site_menu/' . $site_menu_id . '/items/list/' . $site_menu_item_parent_id);
        }

        $site_menu_obj = SiteMenu::factory($site_menu_id);

        $html = SkifPhpRender::renderTemplateBySkifModule(
            'SiteMenu',
            'admin_site_menu_items_move_list.tpl.php',
            array(
                'site_menu_id' => $site_menu_id,
                'parent_id' => $site_menu_item_parent_id,
                'move_item_id' => $move_item_id
            )
        );

        $breadcrumbs_arr = self::getBreadcrumbsArr($site_menu_id, $site_menu_item_parent_id);

        echo SkifPhpRender::renderTemplate(ConfWrapper::value('layout.admin'), array(
                'title' => $site_menu_obj->getName(),
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function moveItemAdminAction($site_menu_id, $site_menu_item_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $destination_parent_item_id = array_key_exists('destination_parent_item_id',
            $_REQUEST) ? $_REQUEST['destination_parent_item_id'] : 0;
        $destination_item_id = array_key_exists('destination_item_id',
            $_REQUEST) ? $_REQUEST['destination_item_id'] : 0;

        $weight = 1;

        $site_menu_item_obj = SiteMenuItem::factory($site_menu_item_id);

        $old_parent_item_id = $site_menu_item_obj->getParentId();

        $site_menu_item_obj->setParentId($destination_parent_item_id);
        if (!$destination_item_id) {
            $site_menu_item_obj->setWeight(1);
            $weight++;
        }
        $site_menu_item_obj->save();

        $children_ids_arr = SiteMenuUtils::getSiteMenuItemIdsArr($site_menu_id, $destination_parent_item_id);

        foreach ($children_ids_arr as $children_item_id) {
            if ($children_item_id == $site_menu_item_id) {
                continue;
            }

            $children_item_obj = SiteMenuItem::factory($children_item_id);

            $children_item_obj->setWeight($weight);
            $children_item_obj->save();

            $weight++;

            if ($children_item_id == $destination_item_id) {
                $site_menu_item_obj->setWeight($weight);
                $site_menu_item_obj->save();

                $weight++;

                continue;
            }
        }

        $weight = 1;

        $children_ids_arr = SiteMenuUtils::getSiteMenuItemIdsArr($site_menu_id, $old_parent_item_id);
        foreach ($children_ids_arr as $children_item_id) {
            $children_item_obj = SiteMenuItem::factory($children_item_id);

            $children_item_obj->setWeight($weight);
            $children_item_obj->save();

            $weight++;
        }

        Messages::setMessage('Пункт меню &laquo;' . $site_menu_item_obj->getName() . '&raquo; перемещен');

        Redirects::redirect('/admin/site_menu/' . $site_menu_id . '/items/list/' . $destination_parent_item_id);
    }

    public function editItemAdminAction($site_menu_id, $site_menu_item_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $site_menu_parent_item_id = array_key_exists('site_menu_parent_item_id',
            $_REQUEST) ? $_REQUEST['site_menu_parent_item_id'] : 0;

        if ($site_menu_item_id != 'new') {
            $site_menu_item_obj = SiteMenuItem::factory($site_menu_item_id);
            $site_menu_parent_item_id = $site_menu_item_obj->getParentId();
        }

        $breadcrumbs_arr = self::getBreadcrumbsArr(
            $site_menu_id,
            $site_menu_parent_item_id
        );

        $html = SkifPhpRender::renderTemplateBySkifModule(
            'SiteMenu',
            'admin_site_menu_item_form_edit.tpl.php',
            array(
                'site_menu_id' => $site_menu_id,
                'site_menu_item_id' => $site_menu_item_id,
                'site_menu_parent_item_id' => $site_menu_parent_item_id
            )
        );

        echo SkifPhpRender::renderTemplate(
            ConfWrapper::value('layout.admin'),
            array(
                'title' => ($site_menu_item_id == 'new') ? 'Добавление пункта меню' : 'Редактирование пункта меню',
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function saveItemAdminAction($site_menu_id, $site_menu_item_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $url = array_key_exists('url', $_REQUEST) ? $_REQUEST['url'] : '';
        $content_id = array_key_exists('content_id', $_REQUEST) ? intval($_REQUEST['content_id']) : null;

        $content_title = array_key_exists('content_title', $_REQUEST) ? $_REQUEST['content_title'] : '';
        if (!$content_title) {
            $content_id = null;
        }

        $parent_id = array_key_exists('parent_id', $_REQUEST) ? $_REQUEST['parent_id'] : 0;
        $weight = array_key_exists('weight', $_REQUEST) ? $_REQUEST['weight'] : 0;
        $is_published = array_key_exists('is_published', $_REQUEST) ? $_REQUEST['is_published'] : 0;

        if ($site_menu_item_id == 'new') {
            $site_menu_item_obj = new SiteMenuItem();
        } else {
            $site_menu_item_obj = SiteMenuItem::factory($site_menu_item_id);
        }

        if (!$name) {
            Messages::setError('Отсутствует название');
        }

        $site_menu_item_obj->setName($name);
        $site_menu_item_obj->setContentId($content_id);
        //$site_menu_item_obj->setWeight($weight);
        $site_menu_item_obj->setParentId($parent_id);
        $site_menu_item_obj->setMenuId($site_menu_id);
        $site_menu_item_obj->setIsPublished($is_published);


        if ($site_menu_item_obj->getContentId()) {
            $content_obj = \WebSK\Skif\Content\Content::factory($site_menu_item_obj->getContentId());
            $url = $content_obj->getUrl();
        }

        if ($url) {
            $url = '/' . ltrim($url, '/');
        }

        $site_menu_item_obj->setUrl($url);

        $site_menu_item_obj->save();


        Messages::setMessage('Изменения в &laquo;' . $site_menu_item_obj->getName() . '&raquo; сохранены');

        Redirects::redirect($site_menu_item_obj->getEditorUrl());
    }

    public function deleteItemAdminAction($site_menu_id, $site_menu_item_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $site_menu_item_obj = SiteMenuItem::factory($site_menu_item_id);

        $site_menu_item_obj->delete();

        Messages::setMessage('Пункт меню ' . $site_menu_item_obj->getName() . ' удален');

        Redirects::redirect('/admin/site_menu/' . $site_menu_id . '/items/list/' . $site_menu_item_obj->getParentId());
    }

    public function listAdminAction()
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $html = SkifPhpRender::renderTemplateBySkifModule('SiteMenu', 'admin_site_menu_list.tpl.php');

        echo SkifPhpRender::renderTemplate(
            ConfWrapper::value('layout.admin'),
            array(
                'title' => 'Менеджер меню',
                'content' => $html,
                'breadcrumbs_arr' => array()
            )
        );
    }

    public function editAdminAction($site_menu_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $html = SkifPhpRender::renderTemplateBySkifModule(
            'SiteMenu',
            'admin_site_menu_form_edit.tpl.php',
            array('site_menu_id' => $site_menu_id)
        );

        echo SkifPhpRender::renderTemplate(
            ConfWrapper::value('layout.admin'),
            array(
                'title' => 'Редактирование меню',
                'content' => $html,
                'breadcrumbs_arr' => array(
                    'Менеджер меню' => '/admin/site_menu'
                )
            )
        );
    }

    public function saveAdminAction($site_menu_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $url = array_key_exists('url', $_REQUEST) ? $_REQUEST['url'] : '';

        if ($site_menu_id == 'new') {
            $site_menu_obj = new SiteMenu();
        } else {
            $site_menu_obj = SiteMenu::factory($site_menu_id);
        }

        if (!$name) {
            Messages::setError('Отсутствует название');
        }

        $site_menu_obj->setName($name);
        $site_menu_obj->setUrl($url);
        $site_menu_obj->save();

        Messages::setMessage('Изменения сохранены');

        Redirects::redirect('/admin/site_menu');
    }

    public function deleteAdminAction($site_menu_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $site_menu_obj = SiteMenu::factory($site_menu_id);

        $site_menu_obj->delete();

        Messages::setMessage('Меню ' . $site_menu_obj->getName() . ' удалено');

        Redirects::redirect('/admin/site_menu');
    }
}
