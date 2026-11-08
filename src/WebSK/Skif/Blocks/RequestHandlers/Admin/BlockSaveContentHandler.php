<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockRole;
use WebSK\Skif\Blocks\BlockRoleService;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockSaveContentHandler extends BaseHandler
{
    use CurrentTemplateIdTrait;

    #[\DI\Attribute\Inject]
    protected BlockService $block_service;

    #[\DI\Attribute\Inject]
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

        $title = \WebSK\CRUD\RequestParams::parsedBody($request, 'title', '');
        $block_obj->setTitle($title);

        $body = \WebSK\CRUD\RequestParams::parsedBody($request, 'body','');
        $block_obj->setBody($body);

        $format = \WebSK\CRUD\RequestParams::parsedBody($request, 'format', 3);
        $block_obj->setFormat($format);

        $pages = \WebSK\CRUD\RequestParams::parsedBody($request, 'pages', '+ ^');
        $block_obj->setPages($pages);

        $is_new = !$block_obj->getId();

        if ($is_new) {
            $template_id = $this->getCurrentTemplateId();

            $block_obj->setTemplateId($template_id);
        }

        $this->block_service->save($block_obj);

        // Roles
        $this->block_service->deleteBlocksRolesByBlockId($block_id);

        $roles_ids_arr = \WebSK\CRUD\RequestParams::parsedBody($request, 'roles', []);
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
        $redirect_to_on_success = \WebSK\CRUD\RequestParams::parsedBody($request, '_redirect_to_on_success', '');
        if ($redirect_to_on_success) {
            if (str_contains($redirect_to_on_success, 'block_id')) {
                $saved_block_id = $block_obj->getId();
                if ($saved_block_id === null) {
                    throw new \UnexpectedValueException('Saved block has no ID');
                }
                $redirect_to_on_success = str_replace('block_id', (string)$saved_block_id, $redirect_to_on_success);
            }

            return $response->withHeader('Location', $redirect_to_on_success)
                ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
        }

        return $response->withHeader('Location', $this->urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]))
            ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
    }
}
