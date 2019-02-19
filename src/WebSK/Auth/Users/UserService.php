<?php

namespace WebSK\Auth\Users;

use WebSK\Skif\Image\ImageConstants;
use WebSK\Skif\Image\ImageManager;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Config\ConfWrapper;

/**
 * Class UserService
 * @method User getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Auth\Users
 */
class UserService extends EntityService
{
    /** @var RoleService */
    protected $role_service;

    /** @var UserRoleService */
    protected $user_role_service;

    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        RoleService $role_service,
        UserRoleService $user_role_service
    ) {
        $this->role_service = $role_service;
        $this->user_role_service = $user_role_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param int $user_id
     * @throws \Exception
     */
    public function deleteUserRolesForUserId(int $user_id)
    {
        $user_roles_ids_arr = $this->user_role_service->getIdsArrByUserId($user_id);

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = $this->user_role_service->getById($user_role_id);
            $this->user_role_service->delete($user_role_obj);
        }
    }

    /**
     * @param int $user_id
     * @return array
     * @throws \Exception
     */
    public function getRoleIdsArrByUserId(int $user_id)
    {
        $user_roles_ids_arr = $this->user_role_service->getIdsArrByUserId($user_id);

        $role_ids_arr = [];

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = $this->user_role_service->getById($user_role_id);
            $role_ids_arr[] = $user_role_obj->getRoleId();
        }

        return $role_ids_arr;
    }

    /**
     * @param User $user_obj
     * @return bool
     * @throws \Exception
     */
    public function deletePhoto(User $user_obj)
    {
        if (!$user_obj->getPhotoPath()) {
            return true;
        }

        $user_obj->setPhoto('');
        $this->save($user_obj);

        $file_path = ConfWrapper::value('site_full_path') . DIRECTORY_SEPARATOR . ImageConstants::IMG_ROOT_FOLDER . DIRECTORY_SEPARATOR . $user_obj->getPhotoPath();
        if (!file_exists($file_path)) {
            return false;
        }

        $image_manager = new ImageManager();
        $image_manager->removeImageFile($user_obj->getPhotoPath());

        return true;
    }

    /**
     * @param InterfaceEntity|User $user_obj
     * @throws \Exception
     */
    public function afterDelete(InterfaceEntity $user_obj)
    {
        $this->deletePhoto($user_obj);
        $this->deleteUserRolesForUserId($user_obj->getId());

        parent::afterDelete($user_obj);
    }

    /**
     * Является ли пользователь администратором
     * @param int $user_id
     * @return bool
     * @throws \Exception
     */
    public function hasRoleAdminByUserId(int $user_id)
    {
        if (in_array(Role::ROLE_ADMIN, $this->getRoleIdsArrByUserId($user_id))) {
            return true;
        }

        return false;
    }

    /**
     * Есть ли у пользователя роль, по обозначению роли
     * @param int $user_id
     * @param string $designation
     * @return bool
     * @throws \Exception
     */
    public function hasRoleByUserIdAndDesignation(int $user_id, string $designation)
    {
        $roles_ids_arr = $this->getRoleIdsArrByUserId($user_id);

        foreach ($roles_ids_arr as $role_id) {
            if (!$role_id) {
                continue;
            }

            $role_obj = $this->role_service->getById($role_id);

            if (trim($role_obj->getDesignation()) == trim($designation)) {
                return true;
            }
        }

        return false;
    }
}
