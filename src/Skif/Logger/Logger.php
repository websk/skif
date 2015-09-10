<?php

namespace Skif\Logger;


class Logger
{

	/**
	 * Если передан не объект, то в базе вместо идентификатора объекта будет хранится строка "not_object"
	 *
	 * @param $object
	 * @param $action
	 */
	static public function logObjectEvent($object, $action)
	{
        $ip_address = \Skif\Util\Network::getClientIpRemoteAddr();
		$entity_id = \Skif\Utils::getFullObjectId($object);
		$serialized_object = serialize($object);

        if (!$ip_address){
            $ip_address = '';
        }

		\Skif\DB\DBWrapper::query(
			"INSERT INTO admin_log (user_id, ts, ip, action, entity_id, object) VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?)",
			array(self::currentUserId(), $ip_address, $action, $entity_id, $serialized_object)
		);
	}

	static public function currentUserId()
	{
		$user_id = \Skif\Users\AuthUtils::getCurrentUserId();

		return $user_id;
	}

	static public function getUrlForObj($obj) {
		$obj_class_name = get_class($obj);
		\Skif\CRUD\Helpers::exceptionIfClassNotImplementsInterface($obj_class_name, 'Skif\Model\InterfaceLoad');

		$obj_id = $obj->getId();

		return '/admin/logger/object_log/' . urlencode($obj_class_name) . '.' . $obj_id;
	}
} 