<?php
/**
 * @var $field_name string
 * @var $field_value string
 * @var $filtered_user_role_id
 * @var $disabled
 * @var $default_value
 */

if (!isset($disabled)) {
    $disabled = false;
}

if (!$field_value && isset($default_value)) {
    $field_value = $default_value;
}

$users_ids_arr = \Skif\Users\UsersUtils::getUsersIdsArr($filtered_user_role_id);
?>
<select id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" class="form-control"<?php echo $disabled ? ' disabled' : '' ?>>
    <option></option>
    <?php
    foreach ($users_ids_arr as $user_id) {
        $user_obj = \Skif\Users\User::factory($user_id);

        echo '<option value="' . $user_id . '"' . ($user_id == $field_value ? ' selected' : '') . '>' . $user_obj->getName() . '</option>';
    }
    ?>
</select>
