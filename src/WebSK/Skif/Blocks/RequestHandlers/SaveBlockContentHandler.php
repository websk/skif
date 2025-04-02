<?php

namespace WebSK\Skif\Blocks\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockRole;
use WebSK\Skif\Blocks\BlockRoleService;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class SaveBlockContentHandler extends BaseHandler
{

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

        $title = array_key_exists('title', $_POST) ? $_POST['title'] : '';
        $block_obj->setTitle($title);

        $body = array_key_exists('body', $_POST) ? $_POST['body'] : '';
        $block_obj->setBody($body);

        $format = array_key_exists('format', $_POST) ? $_POST['format'] : 3;
        $block_obj->setFormat($format);

        $pages = array_key_exists('pages', $_POST) ? $_POST['pages'] : '+ ^';
        $block_obj->setPages($pages);

        $is_new = !$block_obj->getId();

        if ($is_new) {
            $template_id = BlockUtils::getCurrentTemplateId();

            $block_obj->setTemplateId($template_id);
        }

        $this->block_service->save($block_obj);

        // Roles
        $this->block_service->deleteBlocksRolesByBlockId($block_id);

        if (array_key_exists('roles', $_REQUEST)) {
            foreach ($_REQUEST['roles'] as $role_id) {
                $block_role_obj = new BlockRole();
                $block_role_obj->setRoleId($role_id);
                $block_role_obj->setBlockId($block_obj->getId());
                $this->block_role_service->save($block_role_obj);
            }
        }

        // Clear cache
        if ($is_new) {
            BlockUtils::clearBlockIdsArrByPageRegionIdCache($block_obj->getPageRegionId(), $block_obj->getTemplateId());
        }

        Messages::setMessage('Изменения сохранены');

        // Redirects
        if (array_key_exists('_redirect_to_on_success', $_REQUEST) && $_REQUEST['_redirect_to_on_success'] != '') {
            $redirect_to_on_success = $_REQUEST['_redirect_to_on_success'];

            // block_id
            if (strpos($redirect_to_on_success, 'block_id') !== false) {
                $redirect_to_on_success = str_replace('block_id', $block_obj->getId(), $redirect_to_on_success);
            }

            return $response->withHeader('Location', $redirect_to_on_success);
        }

        return $response->withHeader('Location', $block_obj->getEditorUrl());
    }
}