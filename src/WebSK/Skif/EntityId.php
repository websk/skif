<?php

namespace WebSK\Skif;

use WebSK\Entity\InterfaceEntity;

final class EntityId
{
    public static function toString(InterfaceEntity $entity): string
    {
        $id = $entity->getId();
        if ($id === null) {
            throw new \UnexpectedValueException($entity::class . ' has no persistent ID');
        }

        return (string)$id;
    }
}
