<?php

namespace WebSK\Skif\Logger;

use OLOG\FullObjectId;
use Slim\Interfaces\RouterInterface;
use WebSK\Skif\Container;
use WebSK\Entity\InterfaceEntity;

class LoggerRender
{
    /**
     * @param InterfaceEntity $entity_obj
     * @return string
     */
    public static function getLoggerLinkForEntityObj(InterfaceEntity $entity_obj)
    {
        $container = Container::self();

        /** @var RouterInterface $router */
        $router = $container['router'];

        $model_full_id = FullObjectId::getFullObjectId($entity_obj);
        $logger_link = $router->pathFor(
            LoggerRoutes::ROUTE_NAME_ADMIN_LOGGER_OBJECT_ENTRIES_LIST,
            ['object_full_id' => urlencode($model_full_id)]
        );

        return $logger_link;
    }
}
