<?php

namespace WebSK\Skif\Form;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Skif\Logger;
use WebSK\Utils\FullObjectId;

/**
 * Class FormFieldService
 * @method ($exception_if_not_loaded is true ? FormField : FormField|null) getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Skif\Form
 */
class FormFieldService extends EntityService
{
    /** @var FormFieldRepository */
    protected \WebSK\Entity\EntityRepository $repository;

    /**
     * @param int $form_id
     * @return list<int>
     */
    public function getIdsArrByFormId(int $form_id): array
    {
        return $this->repository->findIdsByFormId($form_id);
    }

    /**
     * @param InterfaceEntity|FormField $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|FormField $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
