<?php

namespace Skif\Blocks;


class ControllerBlocks
{
    /**
     * Вывод списка блоков
     */
    public function listAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $message = self::blocksPageActions();

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'blocks_list.tpl.php');
        $page_title_extra = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'blocks_list_title_extra.tpl.php');

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => 'Блоки',
                'content' => $html,
                'page_title_extra' => $page_title_extra,
                'messages_arr' => $message ? array($message) : null
            )
        );
    }

    /**
     * Действия с элементами списка блоков
     * @return string
     */
    static public function blocksPageActions()
    {
        if (array_key_exists('a', $_GET) && ($_GET['a'] == 'disable')
            && array_key_exists('block_id', $_GET) && is_numeric($_GET['block_id'])
        ) {
            return self::disableBlock($_GET['block_id']);
        }

        return '';
    }

    /**
     * Отключение блока
     * @param $block_id
     * @return string
     */
    static public function disableBlock($block_id)
    {
        $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_id);
        if (!$block_obj) {
            return '';
        }

        if ($block_obj->getRegion() == \Skif\Constants::BLOCK_REGION_NONE) {
            return '';
        }

        $prev_region = $block_obj->getRegion();
        $prev_weight = $block_obj->getWeight();

        $restore_url = \Skif\Blocks\ControllerBlocks::getEditorUrl($block_id);
        $restore_url .= '/position?_action=move_block&target_region=' . $prev_region . '&target_weight=' . $prev_weight;

        $sql = "UPDATE blocks SET region='', status = 0 WHERE id=?";
        \Skif\DB\DBWrapper::query( $sql, array($block_obj->getId()));

        \Skif\Logger\Logger::logObjectEvent($block_obj, 'отключение');

        return 'Блок ' . $block_id . ' был выключен.<br><a href="/' . $restore_url . '">Отменить</a>';
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

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'block_edit.tpl.php',
            array('block_id' => $block_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array('Блоки' => '/admin/blocks/list')
            )
        );
    }

    public function aceTabAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        self::actions($block_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'block_ace.tpl.php',
            array('block_id' => $block_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array('Блоки' => '/admin/blocks/list')
            )
        );
    }

    /**
     * @param $block_id_str
     * @return \Skif\Blocks\Block
     */
    static public function getBlockObj($block_id_str)
    {
        if (strpos($block_id_str, 'NEW') !== false) {
            $block_obj =  new \Skif\Blocks\Block();
            return $block_obj;
        }

        $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_id_str);

        if (!$block_obj) {
            \Skif\Http::exit404();
        }

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

        $page_title = $block_obj->getInfo();
        if ($page_title == '') {
            $page_title = $block_obj->getTheme() . '.' . $block_obj->getId();
        }

        $region_for_title = $block_obj->getRegion();

        if ($block_obj->getRegion() == \Skif\Constants::BLOCK_REGION_NONE) {
            $region_for_title = 'отключен';
        }

        $page_title .= ' <span style="color: silver">' . $region_for_title . '</span>';

        return $page_title;
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
        $search_value = '';
        $message = '';

        if (array_key_exists("search", $_POST) && (strlen($_POST["search"]) > 3)) {
            $search_value = $_POST["search"];

            $blocks_ids_arr = \Skif\DB\DBWrapper::readColumn(

                'SELECT id FROM blocks WHERE body LIKE ? LIMIT 100',
                array("%" . $search_value . "%")
            );

            if (count($blocks_ids_arr) == 0) {
                $message = 'Ничего не найдено';
            }
        } else {
            $message = 'Слишком короткий запрос';
        }

        $html .= \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'search_blocks.tpl.php',
            array('blocks_arr' => $blocks_ids_arr, 'message' => $message)
        );

        $page_title_extra = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'search_blocks_title_extra.tpl.php',
            array('search_value' => $search_value)
        );

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => 'Поиск блоков',
                'content' => $html,
                'page_title_extra' => $page_title_extra,
                'breadcrumbs_arr' => array('Блоки' => '/admin/blocks/list')
            )
        );
    }

    /**
     * Дефолтная тема
     * @return string
     */
    static public function getEditorTheme()
    {
        if (array_key_exists('theme', $_REQUEST)) {
            return $_REQUEST['theme'];
        }

        return 'main';
    }

    /**
     * Сохранение расположения блока в регионе
     * @param $block_id
     * @throws \Exception
     */
    static public function moveBlock($block_id)
    {
        $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_id);
        \Skif\Utils::assert($block_obj);

        $target_weight = $_REQUEST['target_weight'];
        $target_region = $_REQUEST['target_region'];

        if (($target_weight == '') || ($target_region == '')) {
            return;
        }

        \Skif\Logger\Logger::logObjectEvent($block_obj, 'перемещение');

        $blocks_arr = \Skif\Blocks\ControllerBlocks::getBlocksIdsArrByTheme($block_obj->getTheme());
        usort($blocks_arr, array('Skif\Blocks\ControllerBlocks', '_block_compare'));

        $arranged_blocks = array();
        $block_inserted = false;

        if ($target_weight == 'FIRST') { // place our block first
            $arranged_blocks[] = array('id' => $block_obj->getId(), 'region' => $target_region);
            $block_inserted = true;
        }

        // copy all blocks except our one - it will is inserted specially
        $last_weight = -1;

        foreach ($blocks_arr as $rblock) {
            if ($rblock['theme'] != $block_obj->getTheme()) {
                continue;
            }

            if ($rblock['region'] != $target_region) {
                continue;
            }

            if ($rblock['id'] != $block_obj->getId()) {
                // if not our block - copy it to arranged blocks
                $arranged_blocks[] = $rblock;
            }

            if ($rblock['weight'] == $target_weight) { // place our block after the block with target weight
                if (!$block_inserted){
                    $arranged_blocks[] = array('id' => $block_obj->getId(), 'region' => $target_region);
                    $block_inserted = true;
                }
            }

            if (($target_weight > $last_weight) && ($target_weight < $rblock['weight'])){
                // блока с запрошенным весом нет, но есть дырка между блоками: у текущего блока вес больше запрошенного, а у предыдущего - меньше
                // такое может быть, если блок какой-то перемещался без перебалансировки весов - например, "отключался" со страницы списка блоков
                // это сработает только если блоки отсортированы по весу, так что обязательно нужно сортировать
                if (!$block_inserted){
                    $arranged_blocks[] = array('id' => $block_obj->getId(), 'region' => $target_region);
                    $block_inserted = true;
                }
            }

            $last_weight = $rblock['weight'];
        }

        // проверяем, что блок удалось вставить (просто защита)
        $block_found = false;
        foreach ($arranged_blocks as $rblock) {
            if (($rblock['id'] == $block_obj->getId()) && ($rblock['region'] == $target_region)) {
                $block_found = true;
            }
        }

        if (!$block_found){
            throw new \Exception('block insertion failed');
        }

        foreach ($arranged_blocks as $i => $rblock) {
            $weight = $i + 1;

            // did not use block->save here - need to load block before saving, extra queries
            // or construct block from _block_rehash result

            $region_to_store = $rblock['region'];
            $status = 1;
            if ($rblock['region'] == \Skif\Constants::BLOCK_REGION_NONE) {
                $region_to_store = '';
                $status = 0;
            }

            \Skif\DB\DBWrapper::query(
                'UPDATE blocks SET weight = ?, region = ?, status = ? WHERE id = ?',
                array($weight, $region_to_store, $status, $rblock['id'])
            );
        }

        \Skif\Blocks\BlockFactory::removeFromCacheById($block_obj->getId());

        $cache_key = \Skif\Blocks\PageRegions::getRegionBlocksCacheKey($block_obj->getTheme(), $block_obj->getRegion());
        \Skif\Cache\CacheWrapper::delete($cache_key);

        \Skif\Http::redirect('/' . \Skif\Blocks\ControllerBlocks::getEditorUrl($block_id) . '/position');
    }

    /**
     * Действия над блоком
     * @param Block $block_id
     */
    static public function actions($block_id)
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
            $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_id);
            \Skif\Utils::assert($block_obj);

            $block_obj->cache = $_POST['cache'];
            $block_obj->save();
            \Skif\Http::redirect('/' . \Skif\Blocks\ControllerBlocks::getEditorUrl($block_id) . '/caching');
        }

        if ($action == 'save_content') {
            self::saveContent($block_id);
        }

        if ($action == 'save_ace') {
            self::saveContentAce($block_id);
        }
    }

    /**
     * Удаление блока
     * @param $block_id
     */
    static function deleteBlock($block_id)
    {
        $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_id);
        \Skif\Utils::assert($block_obj);

        $cache_key = \Skif\Blocks\PageRegions::getRegionBlocksCacheKey($block_obj->getTheme(), $block_obj->getRegion());
        \Skif\Cache\CacheWrapper::delete($cache_key);

        $block_obj->delete();

        \Skif\Http::redirect('/admin/blocks/list');
    }

    static public function fillBlockFromRequest(\Skif\Blocks\Block $block_obj)
    {
        $block_obj->visibility = array_key_exists('visibility', $_POST) ? $_POST['visibility'] : '';
        $block_obj->pages = array_key_exists('pages', $_POST) ? $_POST['pages'] : '+ ^';
        $block_obj->title = array_key_exists('title', $_POST) ? $_POST['title'] : '';
        $block_obj->body = array_key_exists('body', $_POST) ? $_POST['body'] : '';
        $block_obj->info = array_key_exists('info', $_POST) ? $_POST['info'] : '';
        $block_obj->format = array_key_exists('format', $_POST) ? $_POST['format'] : 3;

        return $block_obj;
    }

    /**
     * Сохранение содержимого блока
     * @param $block_id
     * @return bool
     */
    public static function saveContent($block_id)
    {
        $block_obj = self::getBlockObj($block_id);

        $block_obj = self::fillBlockFromRequest($block_obj);

        $theme = \Skif\Blocks\ControllerBlocks::getEditorTheme();
        $block_obj->setTheme($theme);
        $block_save_status = $block_obj->save();

        if (!$block_save_status) {
            throw new \Exception("Название должно быть уникальным.");
        }

        // save roles

        \Skif\DB\DBWrapper::query(
            "DELETE FROM blocks_roles WHERE block_id = ?",
            array($block_obj->getId())
        );

        if (array_key_exists('roles', $_REQUEST)) {
            foreach ($_REQUEST['roles'] as $role_id) {
                \Skif\DB\DBWrapper::query(
                    "INSERT INTO blocks_roles (role_id, block_id) VALUES (?, ?)",
                    array($role_id, $block_obj->getId())
                );
            }
        }

        \Skif\Blocks\BlockFactory::removeFromCacheById($block_obj->getId());

        // redirect to choice region for block

        $redirect_block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_obj->getId());
        \Skif\Utils::assert($redirect_block_obj);

        if( array_key_exists('_redirect_to_on_success', $_REQUEST) &&  $_REQUEST['_redirect_to_on_success'] != '' ){
            $redirect_to_on_success = $_REQUEST['_redirect_to_on_success'];

            // block_id
            if (strpos($redirect_to_on_success, 'block_id') !== false ){
                $redirect_to_on_success = str_replace('block_id', $redirect_block_obj->getId(), $redirect_to_on_success);
            }

            \Skif\Http::redirect('/' . $redirect_to_on_success );

            return true;
        }

        // redirect to new block editor
        \Skif\Http::redirect('/' . \Skif\Blocks\ControllerBlocks::getEditorUrl($redirect_block_obj->getId()));

        return true;
    }

    /**
     * Сохранение содержимого блока из ACE-редактора
     * @param $block_id
     * @return bool
     */
    public static function saveContentAce($block_id)
    {
        $block_obj = self::getBlockObj($block_id);

        $block_obj->body = array_key_exists('body', $_POST) ? $_POST['body'] : '';

        $block_obj->save();

        \Skif\Blocks\BlockFactory::removeFromCacheById($block_obj->getId());

        return true;
    }

    /**
     * Урл страницы редактирования блока
     * @param $block_id
     * @return string
     */
    public static function getEditorUrl($block_id)
    {
        return 'admin/blocks/edit/' . $block_id;
    }

    /**
     * Урл страницы выбора региона
     * @param $block_id_str
     * @return string
     */
    public static function getRegionsListUrl($block_id_str)
    {
        if (strpos($block_id_str, 'NEW') !== false){
            return 'admin/blocks/edit/block_id/region';
        }
        else {
            return 'admin/blocks/edit/' . $block_id_str . '/region';
        }
    }

    /**
     * Выбор кеширования блока
     * @param $block_id
     */
    public function cachingTabAction($block_id)
    {
        // Проверка прав доступа
        //\Skif\Http::exit403If(!\Skif\Users\Auth::isAdmin());

        $block_obj = self::getBlockObj($block_id);

        if (!$block_obj->isLoaded()) {
            $html = 'Во время создания блока вкладка недоступна.';
        } else {
            self::actions($block_id);

            $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'block_caching.tpl.php',
                array('block_id' => $block_id)
            );
        }

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array('Блоки' => '/admin/blocks/list')
            )
        );
    }

    public static function _block_compare($a, $b)
    {
        static $regions;

        $theme_key = \Skif\Blocks\ControllerBlocks::getEditorTheme();

        // Поправка на тему отличную от s2
        if (array_key_exists('theme', $a)) {
            $theme_key = $a['theme'];
        }

        // We need the region list to correctly order by region.
        if (!isset($regions)) {
            $regions = array_flip(array_keys(\Skif\Blocks\PageRegions::getRegionsArrByTheme($theme_key)));
            $regions[\Skif\Constants::BLOCK_REGION_NONE] = count($regions);
        }

        // Sort by region (in the order defined by theme .info file).
        if ((!empty($a['region']) && !empty($b['region'])) && ($place = ($regions[$a['region']] - $regions[$b['region']]))) {
            return $place;
        }

        // Sort by weight.
        $weight = $a['weight'] - $b['weight'];
        if ($weight) {
            return $weight;
        }

        // Sort by title.
        return strcmp($a['info'], $b['info']);
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

        if (!$block_obj->isLoaded()) {
            $html = 'Во время создания блока вкладка недоступна.';
        } else {
            self::actions($block_id);

            $target_region = $block_obj->getRegion();

            if (isset($_GET['target_region'])) {
                $target_region = $_GET['target_region'];
            }

            $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'block_place_in_region.tpl.php',
                array('block_id' => $block_id, 'target_region' => $target_region)
            );
        }

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    'Блоки' => '/admin/blocks/list'
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

        $block_obj = self::getBlockObj($block_id);

        if (!$block_obj->isLoaded()) {
            $html = 'Во время создания блока вкладка недоступна.';
        } else {
            self::actions($block_id);

            $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'block_delete.tpl.php',
                array('block_id' => $block_id)
            );
        }

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    'Блоки' => '/admin/blocks/list'
                )
            )
        );
    }

    /**
     * Выбор региона
     */
    public function chooseRegionTabAction($block_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $block_obj = self::getBlockObj($block_id);

        if (!$block_obj->isLoaded()) {
            $html = 'Во время создания блока вкладка недоступна.';
        } else {
            self::actions($block_id);

            $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Blocks', 'block_choose_region.tpl.php',
                array('block_id' => $block_id)
            );
        }

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    'Блоки' => '/admin/blocks/list'
                )
            )
        );
    }

    public static function same_block($rblock, $block)
    {
        $rblock = (array)$rblock;
        $block = (array)$block;

        if ($rblock['id'] != $block['id']) {
            return false;
        }

        return true;
    }

    /**
     * Массив блоков для темы
     * @param null $theme_key
     * @return array
     */
    public static function getBlocksIdsArrByTheme($theme_key = null) {
        if (is_null($theme_key)){
            $theme_key = \Skif\Blocks\ControllerBlocks::getEditorTheme();
        }

        $query = "SELECT * FROM blocks WHERE theme = ?";
        $res = \Skif\DB\DBWrapper::readAssoc($query, array($theme_key));
        $blocks = array();
        foreach ($res as $row) {
            if ($row['region'] == '') {
                $row['region'] = \Skif\Constants::BLOCK_REGION_NONE;
            }

            $blocks[] = $row;
        }

        return $blocks;
    }
} 