<?php

echo \Skif\PhpTemplate::renderTemplateBySkifModule(
    'Users',
    'profile_form_edit.tpl.php',
    array('user_id' => 'new')
);
?>


