<?php
/**
 * @var $field_name string
 * @var $field_value string
 * @var $filtered_user_role_id
 * @var $disabled
 * @var $default_value
 */

use Websk\Skif\Container;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;

if (!isset($disabled)) {
    $disabled = false;
}

if (!$field_value && isset($default_value)) {
    $field_value = $default_value;
}

$container = Container::self();
$user_service = UsersServiceProvider::getUserService($container);

$users_ids_arr = UsersUtils::getUsersIdsArr($filtered_user_role_id);
?>
<select id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" class="form-control"<?php echo $disabled ? ' disabled' : '' ?>>
    <option></option>
    <?php
    foreach ($users_ids_arr as $user_id) {
        $user_obj = $user_service->getById($user_id);

        echo '<option value="' . $user_id . '"' . ($user_id == $field_value ? ' selected' : '') . '>' . $user_obj->getName() . '</option>';
    }
    ?>
</select>
