<?php
/**
 * @var Form $form_obj
 * @var FormFieldService $form_field_service
 * @var string $form_send_url
 * @var string $captcha_url
 * @var null|User $current_user_obj
 */

use WebSK\Auth\User\User;
use WebSK\Skif\Form\Form;
use WebSK\Skif\Form\FormField;
use WebSK\Skif\Form\FormFieldService;

$form_id = $form_obj->getId();

if ($form_obj->getComment()) {
    ?>
    <p><?php echo $form_obj->getComment(); ?></p>
    <?php
}

$form_field_ids_arr = $form_field_service->getIdsArrByFormId($form_id);
?>
<form method="post" action="<?php echo $form_send_url; ?>" class="form-horizontal">
    <?php
    foreach ($form_field_ids_arr as $form_field_id) {
        $form_field_obj = $form_field_service->getById($form_field_id);

        $field_size = $form_field_obj->getSize();
        if (!$field_size) {
            $field_size = 20;
        }
        if ($field_size > 50) {
            $field_size = 50;
        }

        $name = $form_field_obj->getName();
        if ($form_field_obj->getRequired()) {
            $name = $name . ' *';
        }

        $field_html = '';
        if ($form_field_obj->getType() == FormField::FIELD_TYPE_STRING) {
            $field_html = '<input type=text name="field_' . $form_field_id . '" maxlength="' . $field_size . '" value="" size="' . $field_size . '" class="form-control">';
        } elseif ($form_field_obj->getType() == FormField::FIELD_TYPE_TEXTAREA) {
            $field_html = '<textarea name="field_' . $form_field_id . '" cols="50" rows="' . $field_size . '" class="form-control"></textarea>';
        }
        ?>
        <div class="form-group">
            <label class="col-md-3"><?php echo $name; ?></label>
            <div class="col-md-9"><?php echo $field_html; ?></div>
        </div>
        <?php
    }

    if ($current_user_obj) {
        echo '<input type="hidden" name="email" value="' . $current_user_obj->getEmail() . '">';
    } else {
        ?>
        <div class="form-group">
            <label class="col-md-3">Ваш E-mail *</label>
            <div class="col-md-9"><input type="text" name="email" value="" class="form-control"></div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-3 col-md-9">
                <img src="<?php echo $captcha_url; ?>" border="0" alt="Введите этот защитный код">
                <input type="text" size="5" name="captcha" class="form-control">
                <span class="help-block">Введите код, изображенный на картинке</span>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="form-group">
        <div class="col-md-offset-3 col-md-9">
            <button class="btn btn-primary"><?php echo $form_obj->getButtonLabel() ?: 'Отправить'  ?></button>
            <p class="help-block">* отмечены поля, обязательные для заполнения</p>
        </div>
    </div>
</form>
