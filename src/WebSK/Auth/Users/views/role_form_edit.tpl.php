<?php
/**
 * @var Role $role_obj
 * @var string $save_handler_url
 */

use WebSK\Auth\Users\Role;

?>
<form action="<?php echo $save_handler_url; ?>" method="post" class="form-horizontal">
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
