<?php
$user = \Skif\Auth\AuthHelper::getCurrentUser();

$redirect_url = \Skif\Helpers::getCurrentUrl();

$messages_arr = \Skif\Auth\AuthHelper::getFlashMessages();

if ($user instanceof \Skif\Auth\User) {
    ?>
    <ul class="user-menu">
        <li class="avater">
            <a class="#" href="<?php echo $user->getUrl() ?>" title="<?=$user->display_name?>">
                <img id="user_avatar_image" src="<?php echo \Skif\Helpers::getCdnUrlForImageByPreset($user->getProfileImage(), \Skif\Image\ImagePresets::IMAGE_PRESET_38_38); ?>" alt="">
            </a>
        </li>
        <li><a id="user_display_name" class="#" href="<?php echo $user->getUrl() ?>" title="<?=$user->display_name?>"><?=$user->display_name?></a></li>
    </ul>
<?php

} else {
?>
    <?=implode(' ', $messages_arr)?>
    <ul class="user-menu">
        <li><a id="auth-link-js" class="auth-link" href="#auth">Войти</a></li>
        <div id="auth-popup-js" class="auth-popup">
            <form method="post" action="/auth/social?destination=<?=$redirect_url?>">
                    <ul>
                        <li><input class="auth-btn__fb" type="submit" name="Provider" value="Facebook"/></li>
                        <li><input class="auth-btn__tw" type="submit" name="Provider" value="Twitter"/></li>
                        <li><input class="auth-btn__vk" type="submit" name="Provider" value="Vkontakte"/></li>
                        <li><input class="auth-btn__gp" type="submit" name="Provider" value="Google"/></li>
                        <li><input class="auth-btn__ok" type="submit" name="Provider" value="Odnoklassniki"/></li>
                    </ul>
                <span>После регистрации можно изменить<br />свои никнейм и аватар</span>
            </form>
        </div>
    </ul>

<?php } ?>