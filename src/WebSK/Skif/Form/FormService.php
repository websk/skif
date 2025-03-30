<?php

namespace WebSK\Skif\Form;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Skif\UniqueUrl;
use WebSK\Utils\Assert;
use WebSK\Utils\FullObjectId;
use WebSK\Utils\Transliteration;

/**
 * Class FormService
 * @method Form getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Form
 */
class FormService extends EntityService
{

    /** @var FormRepository */
    protected $repository;

    protected array $ids_by_urls_cache = [];

    /**
     * @param string $url
     * @return ?int
     */
    public function getIdByUrl(string $url): ?int
    {
        if (array_key_exists($url, $this->ids_by_urls_cache)) {
            return $this->ids_by_urls_cache[$url];
        }

        $id = $this->repository->findIdByUrl($url);

        $this->ids_by_urls_cache[$url] = $id;

        return $id;
    }

    /**
     * @param Form|InterfaceEntity $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        if ($entity_obj->getUrl()) {
            $url = '/' . ltrim($entity_obj->getUrl(), '/');

            if ($url != $entity_obj->getUrl()) {
                $url = UniqueUrl::getUniqueUrl($url);
            }
        } else {
            $url = $this->generateUrl($entity_obj);
            $url = '/' . ltrim($url, '/');
        }

        $entity_obj->setUrl($url);

        parent::beforeSave($entity_obj);
    }

    /**
     * @param Form $form_obj
     * @return string
     */
    protected function generateUrl(Form $form_obj): string
    {
        if (!$form_obj->getTitle()) {
            return '';
        }

        $title_for_url = Transliteration::transliteration($form_obj->getTitle());

        $new_url = $title_for_url;
        $new_url = '/' . ltrim($new_url, '/');

        $new_url = substr($new_url, 0, 255);

        $unique_new_url = UniqueUrl::getUniqueUrl($new_url);
        Assert::assert($unique_new_url);

        return $unique_new_url;
    }

    /**
     * @param InterfaceEntity|Form $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|Form $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
