<?php
/**
 * @var $block_id
 */

$block_obj = \Skif\Blocks\ControllerBlocks::getBlockObj($block_id);
?>
<div class="tabs">
    <ul class="nav nav-tabs" style="margin-bottom: 10px;">
        <li class="active"><a href="/admin/blocks/edit/<?= $block_id ?>" class="active">Содержимое и видимость</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/position">Позиция</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/region">Регион</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/caching">Кэширование</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/delete">Удаление блока</a></li>
        <li><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($block_obj));?>" target="_blank">Журнал</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/ace">ACE</a></li>
    </ul>
</div>

<?php
echo \Skif\Util\CHtml::beginForm(\Skif\UrlManager::getUriNoQueryString(), 'post', array('role' => 'form'));
echo \Skif\Util\CHtml::hiddenField('_action', 'save_content');

$theme = \Skif\Blocks\ControllerBlocks::getEditorTheme();
echo \Skif\Util\CHtml::hiddenField('theme', $theme);

echo \Skif\Util\CHtml::hiddenField('_redirect_to_on_success', '');

echo \Skif\Util\TB::formGroup(
    \Skif\Util\CHtml::label('Название для админки (должно быть уникальным)', false) .
    \Skif\Util\Chtml::textField('info', $block_obj->getInfo(), array("class" => "form-control")) .
    '<p class="help-block">Описание выводится в админке блоков.</p>');

echo \Skif\Util\TB::formGroup(
    \Skif\Util\CHtml::label('Текст блока', false) .
    \Skif\Util\Chtml::textArea('body', $block_obj->getBody(), array("class" => "form-control", "rows" => 15)));

$formats_obj_arr = array(
    3 => 'Текст',
    4 => 'HTML',
    5 => 'PHP code'
);

$formats_arr = array();

$current_format = $block_obj->getFormat();

foreach ($formats_obj_arr as $format_id => $format_name) {
    $formats_arr[$format_id] = $format_name;
}


// setting default format to filtered HTML if plain option is not available by role
if ((array_search('plain', $formats_arr) === false) && (!$block_obj->isLoaded())) {
    $current_format = 1;
}

echo \Skif\Util\TB::formGroup(
    "<button type='button' id='phpTestButton' data-toggle='modal' data-target='#phpTestModal' style='margin-bottom: 10px;' class='btn pull-right btn-default'>PHP Тест</button>");

echo \Skif\Util\TB::formGroup(
    \Skif\Util\CHtml::label('Формат ввода', false) .
    \Skif\Util\CHtml::dropDownList('format', $current_format, $formats_arr,
        array('class' => "form-control")));

$items = array();

// Role-based visibility settings

$default_role_options = array();
$query = "SELECT role_id FROM blocks_roles WHERE block_id = ?";
$res_obj_arr = \Skif\DB\DBWrapper::readObjects($query, array($block_obj->getId()));

foreach ($res_obj_arr as $role_obj) {
    $default_role_options[] = $role_obj->role_id;
}

$roles_ids_arr = \Skif\Users\UsersUtils::getRolesIdsArr();
$role_options = array();

foreach ($roles_ids_arr as $role_id) {
    $role_obj = \Skif\Users\Role::factory($role_id);

    $role_options[$role_id] = $role_obj->getName();
}
?>

    <div class="modal fade" id="phpTestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Результаты проверки PHP кода</h4>
                </div>
                <div class="modal-body" id="phpTestResult">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        $("#phpTestButton").click(function () {
            var formBody = $("#body").val();

            $("#phpTestResult").html("");

            $.ajax({
                type: "POST",
                url: "/phptest",
                data: {php: formBody},
                success: function(data) {
                    $("#phpTestResult").html(data);
                },
                error: function(){
                    $("#phpTestResult").html("<div class='alert alert-danger'><strong>Ошибка!</strong> Проверьте код или обратитесь к разработчикам.</div>");
                }
            });

        });
    </script>

    <div class="panel panel-default">
        <div class="panel-heading">
            <a data-toggle="collapse" href="#visibility_roles_panel">Видимость для ролей</a>
        </div>
        <div id="visibility_roles_panel" class="panel-collapse collapse">
            <div class="panel-body">

                <?php
                echo \Skif\Util\TB::formGroup(
                    \Skif\Util\CHtml::checkBoxList('roles', $default_role_options, $role_options));
                ?>

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="format3docs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <p>Одна строка - один фильтр. Каждый фильтр должен начинаться с символов + или - и потом проблела.</p>
                    <p>После символа + или - и пробела указывается маска адреса. + включает показ блока на этих адресах, а - выключает.</p>
                    <p>Вот пример фильтра для болка, который показывается на всех страницах футбола, кроме России.</p>
<pre>
+ Vidy_sporta/Futbol
- Vidy_sporta/Futbol/Russia
</pre>
                    <p>Маска - это регулярное выражение.</p>

                    <p>Т.е. "business/fcp" - это значит business/fcp может входить в адрес в любом месте.</p>
                    <p>"^/business/fcp" - это значит должно входить именно в начале адреса.</p>
                    <p>Обратите внимание, что в новом режиме видимости адреса начинаются со "/".</p>
                    <p>И главная страница - это не "&laquo;front&raquo;", а "^/$".</p>

                    </div>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">Видимость для страниц</div>
        <div class="panel-body">

            <?php
            echo \Skif\Util\CHtml::label('Набор включающих и исключающих фильтров <a data-toggle="modal" href="#format3docs">справка</a>', false);
            echo \Skif\Util\TB::formGroup(
                \Skif\Util\CHtml::hiddenField('visibility', 3));

            echo \Skif\Util\TB::formGroup(
                \Skif\Util\CHtml::label('Список страниц', false) .
                \Skif\Util\CHtml::textArea('pages', $block_obj->getPages(), array("class" => "form-control", 'rows' => 10)));
            ?>

        </div>
    </div>
<?php


// Обычная кнопка submit;
// По клику чистит скрытое поле _redirect_to_on_success(если вернутся по history назад, _redirect_to_on_success может быть записан)
echo \Skif\Util\CHtml::submitButton('Сохранить', array(
    'class' => 'btn btn-default',
    'onclick' => "this.form['_redirect_to_on_success'].value = ''"
));

// Кнопка по клику записывает в скрытое поле _redirect_to_on_success и сабмитит принудительно форму
echo '&nbsp;&nbsp;';
echo \Skif\Util\CHtml::htmlButton('Сохранить и выбрать регион', array(
    'class' => 'btn',
    'onclick' => "this.form['_redirect_to_on_success'].value = '". \Skif\Blocks\ControllerBlocks::getRegionsListUrl($block_id) . "'; this.form.submit()"
));

foreach ($items as $item) {
    $o .= '<div class="form-group">' . $item . '</div>';
}

echo \Skif\Util\CHtml::endForm();
