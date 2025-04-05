<?php

namespace WebSK\Skif\Blocks;

use WebSK\Skif\Content\TemplateService;
use WebSK\Slim\Container;
use WebSK\Views\PhpRender;

/**
 * Class PageRegionsUtils
 * @package WebSK\Skif\Blocks
 */
class PageRegionsUtils
{
    /**
     * @param string $page_region_name
     * @param string $template_name
     * @param string $page_url
     * @return string
     * @throws \Exception
     */
    public static function renderBlocksByPageRegionNameAndTemplateName(
        string $page_region_name,
        string $template_name,
        string $page_url = ''
    ): string {
        $output = '';

        $container = Container::self();

        /**
         * @var TemplateService $template_service
         */
        $template_service = $container->get(TemplateService::class);

        /**
         * @var BlockService $block_service
         */
        $block_service = $container->get(BlockService::class);

        /**
         * @var PageRegionService $page_region_service
         */
        $page_region_service = $container->get(PageRegionService::class);

        $template_id = $template_service->getIdByName($template_name);
        $page_region_id = $page_region_service->getPageRegionIdByNameAndTemplateId($page_region_name, $template_id);

        $blocks_ids_arr = $block_service->getVisibleBlocksIdsArrByRegionId($page_region_id, $template_id, $page_url);

        foreach ($blocks_ids_arr as $block_id) {
            $output .= PhpRender::renderTemplateInViewsDir(
                'block.tpl.php',
                [
                    'block_id' => $block_id,
                    'block_service' => $block_service
                ]
            );
        }

        return $output;
    }
}
