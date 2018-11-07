<?php

namespace WebSK\Skif\Logger;

use WebSK\Entity\BaseEntityService;
use WebSK\Entity\InterfaceEntity;

/**
 * Trait EntityServiceLoggerTrait
 * @see BaseEntityService
 * @package VitrinaTV\Logger
 */
trait EntityServiceLoggerTrait
{
    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там уже подписать
     * остальных подписчиков.
     * Не забыть в переопределенном методе сбросить кэш!
     * @param InterfaceEntity $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEventForCurrentUser(
            $entity_obj,
            'save'
        );
    }

    /**
     * Метод чистки после удаления объекта.
     * Поскольку сущности уже нет в базе, этот метод должен использовать только данные объекта в памяти:
     * - не использовать геттеры (они могут обращаться к базе)
     * - не быть статическим: работает в контексте конкретного объекта
     * @param InterfaceEntity $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEventForCurrentUser(
            $entity_obj,
            'delete'
        );
    }
}
