<?php
/**
 * @var int $form_id
 */

use WebSK\Skif\Form\FormField;
use WebSK\Auth\Auth;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Skif\Form\FormRoutes;
use WebSK\Skif\Form\FormServiceProvider;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

$container = Container::self();

$form_obj = FormServiceProvider::getFormService($container)->getById($form_id);

$form_field_service = FormServiceProvider::getFormFieldService($container);

if ($form_obj->getComment()) {
    ?>
    <p><?php echo $form_obj->getComment(); ?></p>
    <?php
}

$form_field_ids_arr = $form_field_service->getIdsArrByFormId($form_id);
?>
<form method="post" action="<?php echo Router::pathFor(FormRoutes::ROUTE_NAME_FORM_SEND, ['form_id' => $form_id]); ?>" class="form-horizontal">
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
        } else if ($form_field_obj->getType() == FormField::FIELD_TYPE_TEXTAREA) {
            $field_html = '<textarea name="field_' . $form_field_id . '" cols="50" rows="' . $field_size . '" class="form-control"></textarea>';
        }
        ?>
        <div class="form-group">
            <label class="col-md-3"><?php echo $name; ?></label>
            <div class="col-md-9"><?php echo $field_html; ?></div>
        </div>
        <?php
    }

    $current_user_id = Auth::getCurrentUserId();
    if ($current_user_id) {
        $current_user_obj = Auth::getCurrentUserObj();
        echo '<input type="hidden" name="email" value="' . $current_user_obj->getEmail() . '">';
    } else {
        ?>
        <div class="form-group">
            <label class="col-md-3">Ваш E-mail *</label>
            <div class="col-md-9"><input type="text" name="email" value="" class="form-control"></div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-3 col-md-9">
                <img src="<?php echo Router::pathFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE); ?>" border="0" alt="Введите этот защитный код">
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
