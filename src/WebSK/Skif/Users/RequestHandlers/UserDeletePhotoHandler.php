<?php

namespace WebSK\Skif\Users\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersRoutes;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Utils\HTTP;

/**
 * Class UserDeletePhotoHandler
 * @package WebSK\Skif\Users\RequestHandlers
 */
class UserDeletePhotoHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, int $user_id)
    {
        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $destination = $request->getQueryParam('destination', $this->pathFor(UsersRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        if (!$user_service->deletePhoto($user_obj)) {
            Messages::setError('Не удалось удалить фотографию.');
            return $response->withRedirect($destination);
        }

        Messages::setMessage('Фотография была успешно удалена');

        return $response->withRedirect($destination);
    }
}
