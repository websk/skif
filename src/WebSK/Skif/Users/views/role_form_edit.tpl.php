<?php
/**
 * @var Role $role_obj
 */

use WebSK\Skif\Users\Role;

?>
<form action="/admin/users/roles/save/<?php echo $role_obj->getId(); ?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label class="col-md-4 control-label">Название</label>

        <div class="col-md-8">
            <input type="text" name="name" value="<?= $role_obj->getName() ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Обозначение</label>

        <div class="col-md-8">
            <input type="text" name="designation" value="<?= $role_obj->getDesignation() ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>
