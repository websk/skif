<?php

namespace WebSK\Skif\Form;

use WebSK\Entity\EntityService;

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
    public function getIdsArrByFormId(int $form_id)
    {
        return $this->repository->findIdsByFormId($form_id);
    }
}
