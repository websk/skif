<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Skif\Blocks\PageRegionService;

trait BlockEditorPageTitleTrait
{

    /** @Inject */
    protected PageRegionService $page_region_service;

    /**
     * Заголовок страницы редактирования блока
     * @param int $block_id
     * @return string
     */
    public function getBlockEditorPageTitle(int $block_id): string
    {
        $block_obj = BlockUtils::getBlockObj($block_id);

        if (!$block_obj->isLoaded()) {
            return 'Создание блока';
        }

        $page_region_obj = $this->page_region_service->getById($block_obj->getPageRegionId());
        $region_for_title = $page_region_obj->getTitle();

        $page_title = $block_obj->getTitle();
        if ($page_title == '') {
            $page_title = $region_for_title . '. ' . $block_obj->getId();
        }

        $page_title .= '. ' . $region_for_title;

        return $page_title;
    }
}