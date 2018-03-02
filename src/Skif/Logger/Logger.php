<?php

namespace Skif\Logger;

use Skif\CRUD\CRUDUtils;
use Skif\DB\DBWrapper;
use Skif\Model\InterfaceLoad;
use Skif\Users\AuthUtils;
use Skif\Util\Network;
use Skif\Utils;

class Logger
{

    /**
     * Если передан не объект, то в базе вместо идентификатора объекта будет хранится строка "not_object"
     *
     * @param $object
     * @param $action
     */
    public static function logObjectEvent($object, $action)
    {
        $ip_address = Network::getClientIpRemoteAddr();
        $entity_id = Utils::getFullObjectId($object);
        $serialized_object = serialize($object);

        if (!$ip_address) {
            $ip_address = '';
        }

        DBWrapper::query(
            "INSERT INTO admin_log (user_id, ts, ip, action, entity_id, object) 
                    VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?)",
            [self::currentUserId(), $ip_address, $action, $entity_id, $serialized_object]
        );
    }

    /**
     * @return int|null
     */
    public static function currentUserId()
    {
        $user_id = AuthUtils::getCurrentUserId();

        return $user_id;
    }

    /**
     * @param $obj
     * @return string
     */
    public static function getUrlForObj($obj)
    {
        $obj_class_name = get_class($obj);
        CRUDUtils::exceptionIfClassNotImplementsInterface($obj_class_name, InterfaceLoad::class);

        if (!($obj instanceof InterfaceLoad)) {
            return '';
        }

        $obj_id = $obj->getId();

        return '/admin/logger/object_log/' . urlencode($obj_class_name) . '.' . $obj_id;
    }
}
