<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockRole;
use WebSK\Skif\Blocks\BlockRoleService;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockSaveContentHandler extends BaseHandler
{
    use CurrentTemplateIdTrait;

    /** @Inject */
    protected BlockService $block_service;

    /** @Inject */
    protected BlockRoleService $block_role_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $block_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $block_id): ResponseInterface
    {

        $block_obj = $this->block_service->getById($block_id);

        $title = $request->getParam('title', '');
        $block_obj->setTitle($title);

        $body = $request->getParam('body','');
        $block_obj->setBody($body);

        $format = $request->getParam('format', 3);
        $block_obj->setFormat($format);

        $pages = $request->getParam('pages', '+ ^');
        $block_obj->setPages($pages);

        $is_new = !$block_obj->getId();

        if ($is_new) {
            $template_id = $this->getCurrentTemplateId();

            $block_obj->setTemplateId($template_id);
        }

        $this->block_service->save($block_obj);

        // Roles
        $this->block_service->deleteBlocksRolesByBlockId($block_id);

        $roles_ids_arr = $request->getParam('roles', []);
        foreach ($roles_ids_arr as $role_id) {
            $block_role_obj = new BlockRole();
            $block_role_obj->setRoleId($role_id);
            $block_role_obj->setBlockId($block_obj->getId());
            $this->block_role_service->save($block_role_obj);
        }

        // Clear cache
        if ($is_new) {
            $this->block_service->clearIdsArrByPageRegionIdCache($block_obj->getPageRegionId(), $block_obj->getTemplateId());
        }

        Messages::setMessage('Изменения сохранены');

        // Redirects
        $redirect_to_on_success = $request->getParam('_redirect_to_on_success', '');
        if ($redirect_to_on_success) {
            if (str_contains($redirect_to_on_success, 'block_id')) {
                $redirect_to_on_success = str_replace('block_id', $block_obj->getId(), $redirect_to_on_success);
            }

            return $response->withHeader('Location', $redirect_to_on_success);
        }

        return $response->withHeader('Location', $this->urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]));
    }
}