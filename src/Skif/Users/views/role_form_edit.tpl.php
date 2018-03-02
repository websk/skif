<?php
/**
 * @var int $role_id
 */

use Skif\Users\Role;

if ($role_id == 'new') {
    $users_role_obj = new Role;
} else {
    $users_role_obj = Role::factory($role_id);
}

?>
<form action="/admin/users/roles/save/<?php echo $role_id; ?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label class="col-md-4 control-label">Название</label>

        <div class="col-md-8">
            <input type="text" name="name" value="<?= $users_role_obj->getName() ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Обозначение</label>

        <div class="col-md-8">
            <input type="text" name="designation" value="<?= $users_role_obj->getDesignation() ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>
