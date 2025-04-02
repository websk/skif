<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\DB\DBWrapper;
use WebSK\Logger\Logger;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Container;
use WebSK\Utils\FullObjectId;
use WebSK\Utils\Messages;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;
use WebSK\Views\PhpRender;

/**
 * Class ControllerBlocks
 * @package WebSK\Skif\Blocks
 */
class ControllerBlocks
{

    /**
     * URL страницы со списком блоков
     * @return string
     */
    public static function getBlocksListUrl(): string
    {
        return '/admin/blocks';
    }

    /**
     * URL страницы выбора региона
     * @param string $block_id_str
     * @return string
     */
    public static function getRegionsListUrl(string $block_id_str): string
    {
        if (strpos($block_id_str, 'new') !== false) {
            return '/admin/blocks/edit/block_id/region';
        } else {
            return '/admin/blocks/edit/' . $block_id_str . '/region';
        }
    }

    public function changeTemplateAction(int  $template_id): void
    {
        BlockUtils::setCurrentTemplateId($template_id);

        Messages::setMessage('Тема изменена');

        Redirects::redirect(\WebSK\Skif\Blocks\ControllerBlocks::getBlocksListUrl());
    }

    /**
     * @param int $block_id
     * @return Block
     */
    public static function getBlockObj(int $block_id): Block
    {
        if ($block_id == 'new') {
            return new Block();
        }

        $container = Container::self();
        $block_service = $container->get(BlockService::class);

        return $block_service->getById($block_id);
    }

    /**
     * Заголовок страницы редактирования блока
     * @param int $block_id
     * @return string
     */
    static public function getBlockEditorPageTitle(int $block_id): string
    {
        $block_obj = self::getBlockObj($block_id);

        if (!$block_obj->isLoaded()) {
            return 'Создание блока';
        }

        $container = Container::self();
        $page_region_service = $container->get(PageRegionService::class);

        $page_region_obj = $page_region_service->getById($block_obj->getPageRegionId());
        $region_for_title = $page_region_obj->getTitle();

        $page_title = $block_obj->getTitle();
        if ($page_title == '') {
            $page_title = $region_for_title . '. ' . $block_obj->getId();
        }

        $page_title .= '. ' . $region_for_title;

        return $page_title;
    }

    /**
     * Вывод списка блоков
     */
    public function listAction(): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        self::blocksPageActions();

        $html = PhpRender::renderTemplateInViewsDir(
            'blocks_list.tpl.php'
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
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
    public static function blocksPageActions(): void
    {
        if (array_key_exists('a', $_GET) && ($_GET['a'] == 'disable')
            && array_key_exists('block_id', $_GET) && is_numeric($_GET['block_id'])
        ) {
            self::disableBlock($_GET['block_id']);
        }
    }

    /**
     * Отключение блока
     * @param int $block_id
     */
    public static function disableBlock(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $block_obj = Block::factory($block_id);

        if ($block_obj->getPageRegionId() == PageRegion::BLOCK_REGION_NONE) {
            return;
        }

        $prev_region = $block_obj->getPageRegionId();
        $prev_weight = $block_obj->getWeight();

        $restore_url = $block_obj->getEditorUrl();
        $restore_url .= '/position?_action=move_block&target_region=' . $prev_region . '&target_weight=' . $prev_weight;

        $block_obj->setPageRegionId(PageRegion::BLOCK_REGION_NONE);
        $block_obj->save();

        BlockUtils::clearBlockIdsArrByPageRegionIdCache($prev_region, $block_obj->getTemplateId());
        BlockUtils::clearBlockIdsArrByPageRegionIdCache(PageRegion::BLOCK_REGION_NONE, $block_obj->getTemplateId());

        Logger::logObjectEvent($block_obj, 'отключение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));

        Messages::setWarning('Блок &laquo;' . $block_obj->getTitle() . '&raquo; был выключен. <a href="' . $restore_url . '">Отменить</a>');

        Redirects::redirect(self::getBlocksListUrl());
    }

    /**
     * Редактирование блока
     * @param int $block_id
     */
    public function editAction(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        self::actions($block_id);

        $html = PhpRender::renderTemplateInViewsDir(
            'block_edit.tpl.php',
            array('block_id' => $block_id)
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'breadcrumbs_arr' => array('Блоки' => self::getBlocksListUrl())
            )
        );
    }

    /**
     * Действия над блоком
     * @param int $block_id
     */
    public static function actions(int $block_id): void
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
            self::saveCaching($block_id);
        }

        if ($action == 'save_content') {
            //self::saveContent($block_id);
        }
    }

    /**
     * Выбор кеширования блока
     * @param int $block_id
     */
    public function cachingTabAction(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        self::actions($block_id);

        $html = PhpRender::renderTemplateInViewsDir(
            'block_caching.tpl.php',
            array('block_id' => $block_id)
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'breadcrumbs_arr' => array('Блоки' => self::getBlocksListUrl())
            )
        );
    }

    public static function saveCaching(int $block_id): void
    {
        $block_obj = Block::factory($block_id);

        $block_obj->setCache($_POST['cache']);
        $block_obj->save();

        Messages::setMessage('Изменения сохранены');

        Redirects::redirect($block_obj->getEditorUrl() . '/caching');
    }

    /**
     * Выбор расположения блока в регионе
     * @param int $block_id
     */
    public function placeInRegionTabAction(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $block_obj = self::getBlockObj($block_id);

        self::actions($block_id);

        $target_region_id = $block_obj->getPageRegionId();

        if (isset($_GET['target_region'])) {
            $target_region_id = $_GET['target_region'] ?: null;
        }

        $html = PhpRender::renderTemplateInViewsDir(
            'block_place_in_region.tpl.php',
            array('block_id' => $block_id, 'target_region' => $target_region_id)
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'breadcrumbs_arr' => array(
                    'Блоки' => self::getBlocksListUrl()
                )
            )
        );
    }

    /**
     * Сохранение расположения блока в регионе
     * @param int $block_id
     * @throws \Exception
     */
    static public function moveBlock(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $target_weight = $_REQUEST['target_weight'];
        $target_region_id = $_REQUEST['target_region'] ?: null;

        if ($target_weight == '') {
            return;
        }

        $container = Container::self();
        $block_service = $container->get(BlockService::class);

        $block_obj = $block_service->getById($block_id);

        $source_region = $block_obj->getPageRegionId();
        $block_obj->setPageRegionId($target_region_id);

        Logger::logObjectEvent($block_obj, 'перемещение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));

        $blocks_ids_arr = $block_service->getBlockIdsArrByPageRegionId($target_region_id, $block_obj->getTemplateId());

        /** @var Block[] $arranged_blocks */
        $arranged_blocks = [];

        $block_inserted = false;

        if ($target_weight == 'FIRST') { // place our block first
            $arranged_blocks[] = $block_obj;
            $block_inserted = true;
        }

        // copy all blocks except our one - it will is inserted specially
        $last_weight = -1;

        foreach ($blocks_ids_arr as $other_block_id) {
            $other_block_obj = $block_service->getById($other_block_id);

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
                ($other_block_obj->getId() == $block_obj->getId())
                && ($other_block_obj->getPageRegionId() == $target_region_id)
            ) {
                $block_found = true;
            }
        }

        if (!$block_found) {
            throw new \Exception('block insertion failed');
        }

        foreach ($arranged_blocks as $i => $other_block_obj) {
            $weight = $i + 1;

            $other_block_obj->setWeight($weight);
            $block_service->save($other_block_obj);
        }


        if ($source_region != $block_obj->getPageRegionId()) {
            BlockUtils::clearBlockIdsArrByPageRegionIdCache($source_region, $block_obj->getTemplateId());
        }
        BlockUtils::clearBlockIdsArrByPageRegionIdCache($block_obj->getPageRegionId(), $block_obj->getTemplateId());


        Messages::setMessage('Блок &laquo;' . $block_obj->getTitle() . '&raquo; перемещен');

        Redirects::redirect($block_obj->getEditorUrl() . '/position');
    }

    /**
     * Выбор региона
     * @param int $block_id
     */
    public function chooseRegionTabAction(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        self::actions($block_id);

        $html = PhpRender::renderTemplateInViewsDir(
            'block_choose_region.tpl.php',
            array('block_id' => $block_id)
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'breadcrumbs_arr' => array(
                    'Блоки' => self::getBlocksListUrl()
                )
            )
        );
    }

    /**
     * Удаление блока
     * @param int $block_id
     */
    public function deleteTabAction(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        self::actions($block_id);

        $html = PhpRender::renderTemplateInViewsDir(
            'block_delete.tpl.php',
            array('block_id' => $block_id)
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => self::getBlockEditorPageTitle($block_id),
                'content' => $html,
                'breadcrumbs_arr' => array(
                    'Блоки' => self::getBlocksListUrl()
                )
            )
        );
    }

    /**
     * Удаление блока
     * @param int $block_id
     * @throws \Exception
     */
    public static function deleteBlock(int $block_id): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $block_obj = Block::factory($block_id);

        $block_name = $block_obj->getTitle();

        $block_obj->delete();

        Messages::setMessage('Блок &laquo;' . $block_name . '&raquo; удален');

        Redirects::redirect(self::getBlocksListUrl());
    }

    /**
     * Поиск блоков
     */
    public function searchAction(): void
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $html = '';

        $blocks_ids_arr = array();
        $search_value = $_POST["search"];

        $template_id = BlockUtils::getCurrentTemplateId();

        if ((mb_strlen($_POST["search"]) > 3)) {
            $blocks_ids_arr = DBWrapper::readColumn(
                "SELECT id FROM " . Block::DB_TABLE_NAME . " WHERE body LIKE ? AND template_id = ? LIMIT 100",
                array("%" . str_replace('\\', '\\\\', $search_value) . "%", $template_id)
            );

            if (count($blocks_ids_arr) == 0) {
                Messages::SetWarning('Ничего не найдено');
            }
        } else {
            Messages::SetWarning('Слишком короткий запрос');
        }

        $html .= PhpRender::renderTemplateInViewsDir(
            'search_blocks.tpl.php',
            array(
                'block_ids_arr' => $blocks_ids_arr
            )
        );

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => 'Поиск блоков',
                'content' => $html,
                'breadcrumbs_arr' => array('Блоки' => self::getBlocksListUrl())
            )
        );
    }
}
