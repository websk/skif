<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\User\RoleService;
use WebSK\Skif\Blocks\BlockRoleService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class BlockEditorContentHandler extends BaseHandler
{
    use BlockEditorPageTitleTrait;

    /** @Inject */
    protected BlockRoleService $block_role_service;

    /** @Inject */
    protected RoleService $role_service;


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $block_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $block_id): ResponseInterface
    {
        $block_obj = $this->block_service->getById($block_id, false);

        if (!$block_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $content_html = PhpRender::renderTemplateInViewsDir(
            'block_edit.tpl.php',
            [
                'block_id' => $block_id,
                'block_service' => $this->block_service,
                'block_role_service' => $this->block_role_service,
                'role_service' => $this->role_service
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($this->getBlockEditorPageTitle($block_id));
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Блоки', $this->urlFor(BlockListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }

}