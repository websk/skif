<?php

namespace Skif\Blocks;


class ControllerBlocks
{
    /**
     * URL страницы со списком блоков
     * @return string
     */
    public static function getBlocksListUrl()
    {
        return '/admin/blocks';
    }

    /**
     * URL страницы выбора региона
     * @param $block_id_str
     * @return string
     */
    public static function getRegionsListUrl($block_id_str)
    {
        if (strpos($block_id_str, 'new') !== false) {
            return '/admin/blocks/edit/block_id/region';
        } else {
            return '/admin/blocks/edit/' . $block_id_str . '/region';
        }
    }

    /**
     * Тема
     * @return string
     */
    static public function getCurrentTemplateId()
    {
        if (array_key_exists('skif_blocks_current_template_id', $_COOKIE)) {
            return $_COOKIE['skif_blocks_current_template_id'];
        }

        return 1;
    }

    public static function setEditorTheme($period)
    {
        $delta = null;
        setcookie('skif_blocks_theme', $period, $delta, '/');

        return true;
    }

    /**
     * @param $block_id_str
     * @return \Skif\Blocks\Block
     */
    static public function getBlockObj($block_id_str)
    {
        if (strpos($block_id_str, 'new') !== false) {
            $block_obj = new \Skif\Blocks\Block();
            return $block_obj;
        }

        $block_obj = \Skif\Blocks\Block::factory($block_id_str);

        return $block_obj;
    }

    /**
     * Заголовок страницы редактирования блока
     * @param $block_id
     * @return string
     */
    static public function getBlockEditorPageTitle($block_id)
    {
        $block_obj = \Skif\Blocks\ControllerBlocks::getBlockObj($block_id);

        if (!$block_obj->isLoaded()) {
            return 'Создание блока';
        }

        $page_region_obj = \Skif\Blocks\PageRegion::factory($block_obj->getPageRegionId());
        $region_for_title = $page_region_obj->getTitle();

        $page_title = $block_obj->getInfo();
        if ($page_title == '') {
            $page_title = $region_for_title . '.' . $block_obj->getId();
        }

        if ($block_obj->getPageRegionId() == \Skif\Constants::BLOCK_REGION_NONE) {
            $region_for_title = 'отключен';
        }

        $page_title .= ' <span class="badge">' . $region_for_title . '</span>';

        return $page_title;
    }

    /**
     * Вывод списка блоков
     */
    public function listAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        self::blocksPageActions();

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'blocks_list.tpl.php'
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => 'Блоки',
                'content' => $html,
            )
        );
    }

    /**
     * Действия с элементами списка блоков
     * @return string
     */
    public static function blocksPageActions()
    {
        if (array_key_exists('a', $_GET) && ($_GET['a'] == 'disable')
            && array_key_exists('block_id', $_GET) && is_numeric($_GET['block_id'])
        ) {
            self::disableBlock($_GET['block_id']);
        }
    }

    /**
     * Отключение блока
     * @param $block_id
     */
    public static function disableBlock($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $block_obj = \Skif\Blocks\Block::factory($block_id);

        if ($block_obj->getRegion() == \Skif\Constants::BLOCK_REGION_NONE) {
            return;
        }

        $prev_region = $block_obj->getRegion();
        $prev_weight = $block_obj->getWeight();

        $restore_url = $block_obj->getEditorUrl();
        $restore_url .= '/position?_action=move_block&target_region=' . $prev_region . '&target_weight=' . $prev_weight;

        $block_obj->setRegion(\Skif\Constants::BLOCK_REGION_NONE);
        $block_obj->setStatus(0);
        $block_obj->save();

        \Skif\Blocks\BlockUtils::clearBlockIdsArrByPageRegionIdCache($prev_region);
        \Skif\Blocks\BlockUtils::clearBlockIdsArrByPageRegionIdCache(\Skif\Constants::BLOCK_REGION_NONE);

        \Skif\Logger\Logger::logObjectEvent($block_obj, 'отключение');

        \Skif\Messages::setWarning('Блок &laquo;' . $block_obj->getInfo() . '&raquo; был выключен. <a href="' . $restore_url . '">Отменить</a>');

        \Skif\Http::redirect(\Skif\Blocks\ControllerBlocks::getBlocksListUrl());
    }

    /**
     * Редактирование блока
     * @param $block_id
     */
    public function editAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        self::actions($block_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'block_edit.tpl.php',
            array('block_id' => $block_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array('Блоки' => \Skif\Blocks\ControllerBlocks::getBlocksListUrl())
            )
        );
    }

    /**
     * Действия над блоком
     * @param Block $block_id
     */
    public static function actions($block_id)
    {
        $action = '';

        if (array_key_exists('_action', $_REQUEST)) {
            $action = $_REQUEST['_action'];
        }

        if ($action == '') {
            return;
        }

        if ($action == 'delete_block') {
            self::deleteBlock($block_id);
        }

        if ($action == 'move_block') {
            self::moveBlock($block_id);
        }

        if ($action == 'save_caching') {
            \Skif\Blocks\ControllerBlocks::saveCaching($block_id);
        }

        if ($action == 'save_content') {
            self::saveContent($block_id);
        }
    }

    /**
     * Сохранение содержимого блока
     * @param $block_id
     */
    public static function saveContent($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $block_obj = self::getBlockObj($block_id);

        $info = array_key_exists('info', $_POST) ? $_POST['info'] : '';
        $block_obj->setInfo($info);

        $body = array_key_exists('body', $_POST) ? $_POST['body'] : '';
        $block_obj->setBody($body);

        $format = array_key_exists('format', $_POST) ? $_POST['format'] : 3;
        $block_obj->setFormat($format);

        $pages = array_key_exists('pages', $_POST) ? $_POST['pages'] : '+ ^';
        $block_obj->setPages($pages);

        $is_new = !$block_obj->getId();

        if ($is_new) {
            $template_id = \Skif\Blocks\ControllerBlocks::getCurrentTemplateId();

            $block_obj->setTemplateId($template_id);
        }

        $block_obj->save();


        // Roles
        $block_obj->deleteBlocksRoles();

        if (array_key_exists('roles', $_REQUEST)) {
            foreach ($_REQUEST['roles'] as $role_id) {
                $block_role_obj = new \Skif\Blocks\BlockRole();
                $block_role_obj->setRoleId($role_id);
                $block_role_obj->setBlockId($block_obj->getId());
                $block_role_obj->save();
            }
        }

        // Clear cache
        if ($is_new) {
            \Skif\Blocks\BlockUtils::clearBlockIdsArrByPageRegionIdCache($block_obj->getPageRegionId());
        }


        \Skif\Messages::setMessage('Изменения сохранены');

        // Redirects
        if (array_key_exists('_redirect_to_on_success', $_REQUEST) && $_REQUEST['_redirect_to_on_success'] != '') {
            $redirect_to_on_success = $_REQUEST['_redirect_to_on_success'];

            // block_id
            if (strpos($redirect_to_on_success, 'block_id') !== false) {
                $redirect_to_on_success = str_replace('block_id', $block_obj->getId(), $redirect_to_on_success);
            }

            \Skif\Http::redirect($redirect_to_on_success);
        }

        \Skif\Http::redirect($block_obj->getEditorUrl());
    }

    /**
     * Выбор кеширования блока
     * @param $block_id
     */
    public function cachingTabAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        self::actions($block_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'block_caching.tpl.php',
            array('block_id' => $block_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array('Блоки' => \Skif\Blocks\ControllerBlocks::getBlocksListUrl())
            )
        );
    }

    public static function saveCaching($block_id)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        $block_obj->setCache($_POST['cache']);
        $block_obj->save();

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect($block_obj->getEditorUrl() . '/caching');
    }

    /**
     * Выбор расположения блока в регионе
     * @param $block_id
     */
    public function placeInRegionTabAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $block_obj = self::getBlockObj($block_id);

        self::actions($block_id);

        $target_region = $block_obj->getPageRegionId();

        if (isset($_GET['target_region'])) {
            $target_region = $_GET['target_region'];
        }

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'block_place_in_region.tpl.php',
            array('block_id' => $block_id, 'target_region' => $target_region)
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    'Блоки' => \Skif\Blocks\ControllerBlocks::getBlocksListUrl()
                )
            )
        );
    }

    /**
     * Сохранение расположения блока в регионе
     * @param $block_id
     * @throws \Exception
     */
    static public function moveBlock($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $target_weight = $_REQUEST['target_weight'];
        $target_region = $_REQUEST['target_region'];

        if (($target_weight == '') || ($target_region == '')) {
            return;
        }

        $block_obj = \Skif\Blocks\Block::factory($block_id);

        $source_region = $block_obj->getPageRegionId();
        $block_obj->setPageRegionId($target_region);

        \Skif\Logger\Logger::logObjectEvent($block_obj, 'перемещение');

        $blocks_ids_arr = \Skif\Blocks\BlockUtils::getBlockIdsArrByTemplateId($block_obj->getTemplateId());

        /** @var \Skif\Blocks\Block[] $arranged_blocks */
        $arranged_blocks = array();

        $block_inserted = false;

        if ($target_weight == 'FIRST') { // place our block first
            $arranged_blocks[] = $block_obj;
            $block_inserted = true;
        }

        // copy all blocks except our one - it will is inserted specially
        $last_weight = -1;

        foreach ($blocks_ids_arr as $other_block_id) {
            $other_block_obj = \Skif\Blocks\Block::factory($other_block_id);

            if ($other_block_obj->getPageRegionId() != $target_region) {
                continue;
            }

            if ($other_block_obj->getId() != $block_obj->getId()) {
                // if not our block - copy it to arranged blocks
                $arranged_blocks[] = $other_block_obj;
            }

            if ($other_block_obj->getWeight() == $target_weight) { // place our block after the block with target weight
                if (!$block_inserted) {
                    $arranged_blocks[] = $block_obj;
                    $block_inserted = true;
                }
            }

            if (($target_weight > $last_weight) && ($target_weight < $other_block_obj->getWeight())) {
                // блока с запрошенным весом нет, но есть дырка между блоками: у текущего блока вес больше запрошенного, а у предыдущего - меньше
                // такое может быть, если блок какой-то перемещался без перебалансировки весов - например, "отключался" со страницы списка блоков
                // это сработает только если блоки отсортированы по весу, так что обязательно нужно сортировать
                if (!$block_inserted) {
                    $arranged_blocks[] = $block_obj;
                    $block_inserted = true;
                }
            }

            $last_weight = $other_block_obj->getWeight();
        }

        // проверяем, что блок удалось вставить (просто защита)
        $block_found = false;
        foreach ($arranged_blocks as $other_block_obj) {
            if (
                ($other_block_obj->getId() == $block_obj->getId()) &&
                ($other_block_obj->getPageRegionId() == $target_region)
            ) {
                $block_found = true;
            }
        }

        if (!$block_found) {
            throw new \Exception('block insertion failed');
        }

        foreach ($arranged_blocks as $i => $other_block_obj) {
            $weight = $i + 1;

            $status = 1;
            $region_to_store = $other_block_obj->getPageRegionId();

            if ($region_to_store == \Skif\Constants::BLOCK_REGION_NONE) {
                $status = 0;
            }

            $other_block_obj->setWeight($weight);
            $other_block_obj->setPageRegionId($region_to_store);
            $other_block_obj->setStatus($status);
            $other_block_obj->save();
        }

        \Skif\Blocks\Block::removeObjFromCacheById($block_obj->getId());

        if ($source_region != $block_obj->getPageRegionId()) {
            \Skif\Blocks\BlockUtils::clearBlockIdsArrByPageRegionIdCache($source_region);
        }
        \Skif\Blocks\BlockUtils::clearBlockIdsArrByPageRegionIdCache($block_obj->getPageRegionId());

        \Skif\Messages::setMessage('Блок &laquo;' . $block_obj->getInfo() . '&raquo; перемещен');

        \Skif\Http::redirect($block_obj->getEditorUrl() . '/position');
    }

    /**
     * Выбор региона
     */
    public function chooseRegionTabAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        self::actions($block_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'block_choose_region.tpl.php',
            array('block_id' => $block_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    'Блоки' => \Skif\Blocks\ControllerBlocks::getBlocksListUrl()
                )
            )
        );
    }

    /**
     * Удаление блока
     * @param $block_id
     */
    public function deleteTabAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        self::actions($block_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'block_delete.tpl.php',
            array('block_id' => $block_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    'Блоки' => \Skif\Blocks\ControllerBlocks::getBlocksListUrl()
                )
            )
        );
    }

    /**
     * Удаление блока
     * @param $block_id
     * @throws \Exception
     */
    public static function deleteBlock($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $block_obj = \Skif\Blocks\Block::factory($block_id);

        $block_name = $block_obj->getInfo();

        $block_obj->delete();

        \Skif\Messages::setMessage('Блок &laquo;' . $block_name . '&raquo; удален');

        \Skif\Http::redirect(\Skif\Blocks\ControllerBlocks::getBlocksListUrl());
    }

    /**
     * Поиск блоков
     */
    public function searchAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $html = '';

        $blocks_ids_arr = array();
        $search_value = $_POST["search"];

        $theme_key = \Skif\Blocks\ControllerBlocks::getCurrentTemplateId();

        if ((mb_strlen($_POST["search"]) > 3)) {
            $blocks_ids_arr = \Skif\DB\DBWrapper::readColumn(
                'SELECT id FROM blocks WHERE body LIKE ? AND theme = ? LIMIT 100',
                array("%" . str_replace('\\', '\\\\', $search_value) . "%", $theme_key)
            );

            if (count($blocks_ids_arr) == 0) {
                \Skif\Messages::SetWarning('Ничего не найдено');
            }
        } else {
            \Skif\Messages::SetWarning('Слишком короткий запрос');
        }

        $html .= \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Blocks',
            'search_blocks.tpl.php',
            array(
                'block_ids_arr' => $blocks_ids_arr
            )
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => 'Поиск блоков',
                'content' => $html,
                'breadcrumbs_arr' => array('Блоки' => \Skif\Blocks\ControllerBlocks::getBlocksListUrl())
            )
        );
    }

}