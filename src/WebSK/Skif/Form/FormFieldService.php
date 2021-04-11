<?php

namespace WebSK\Skif\Form;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;

/**
 * Class FormFieldService
 * @method FormField getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Form
 */
class FormFieldService extends EntityService
{
    /** @var FormFieldRepository */
    protected $repository;

    /**
     * @param int $form_id
     * @return array
     */
    public function getIdsArrByFormId(int $form_id): array
    {
        return $this->repository->findIdsByFormId($form_id);
    }

    /**
     * @param InterfaceEntity|FormField $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|FormField $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
