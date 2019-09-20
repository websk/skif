<?php
/**
 * @var $form_id
 */

use WebSK\Auth\User\UserServiceProvider;
use WebSK\Skif\Form\Form;
use WebSK\Skif\Form\FormController;
use WebSK\Skif\Form\FormField;
use WebSK\Auth\Auth;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

$form_obj = Form::factory($form_id);

if ($form_obj->getComment()) {
    ?>
    <p><?php echo $form_obj->getComment(); ?></p>
    <?php
}

$form_field_ids_arr = $form_obj->getFormFieldIdsArr();
?>
<form method="post" action="<?php echo FormController::getSendUrl($form_id); ?>" class="form-horizontal">
    <?php
    foreach ($form_field_ids_arr as $form_field_id) {
        $form_field_obj = FormField::factory($form_field_id);

        $length = $form_field_obj->getSize();
        if (!$length) {
            $length = 20;
        }
        if ($length > 50) {
            $length = 50;
        }

        $name = $form_field_obj->getName();
        if ($form_field_obj->getStatus()) {
            $name = $name . ' *';
        }

        $field_html = '';
        if ($form_field_obj->getType() == FormField::FIELD_TYPE_STRING) {
            $field_html = '<input type=text name="field_' . $form_field_id . '" maxlength="' . $length . '" value="" size="' . $length . '" class="form-control">';
        } else if ($form_field_obj->getType() == FormField::FIELD_TYPE_TEXTAREA) {
            $field_html = '<textarea name="field_' . $form_field_id . '" cols="50" rows="' . $length . '" class="form-control"></textarea>';
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
        $container = Container::self();
        $user_service = UserServiceProvider::getUserService($container);

        $current_user_obj = $user_service->getById($current_user_id);
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
            <button class="btn btn-primary"><?php echo $form_obj->getButtonLabel(); ?></button>
            <p class="help-block">* отмечены поля, обязательные для заполнения</p>
        </div>
    </div>
</form>
