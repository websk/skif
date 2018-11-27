<?php
/**
 * @var $block_id
 */

use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Skif\Blocks\ControllerBlocks;
use WebSK\Skif\PhpRender;
use Websk\Slim\Container;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;
use Websk\Skif\Path;

$block_obj = ControllerBlocks::getBlockObj($block_id);

echo PhpRender::renderTemplateBySkifModule(
    'Blocks',
    'block_edit_menu.tpl.php',
    array('block_id' => $block_id)
);

$items = [];
?>
    <form role="form" action="<?php echo $block_obj->getEditorUrl(); ?>" method="post" id="edit_form">
        <input type="hidden" value="save_content" name="_action" id="_action"/>
        <input type="hidden" value="" name="_redirect_to_on_success" id="_redirect_to_on_success"/>

        <div class="form-group">
            <label>Название</label>
            <input class="form-control" type="text" value="<?php echo $block_obj->getTitle(); ?>" name="title" id="title"/>

            <p class="help-block">Описание выводится в админке блоков.</p>
        </div>

        <style type="text/css" media="screen">
            #editor {
                height: 500px;
                font-size: 100%;
            }
        </style>

        <div class="form-group">
            <label>Текст блока</label>
            <div id="editor"></div>
            <input type="hidden" value="" name="body" id="body" />
        </div>

        <div class="form-group">
            <label>Формат ввода</label>
            <select class="form-control" name="format" id="format">
                <?php
                $formats_arr = BlockUtils::getFormatsArr();

                $current_format = $block_obj->getFormat();
                if (!$block_obj->isLoaded()) {
                    $current_format = 1;
                }

                foreach ($formats_arr as $format_id => $format_name) {
                    ?>
                    <option value="<?php echo $format_id; ?>"<?php echo ($current_format == $format_id ? ' selected="selected"' : '')?>>
                        <?php echo $format_name; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <a data-toggle="collapse" href="#visibility_roles_panel">Видимость для ролей</a>
            </div>
            <div id="visibility_roles_panel" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-group">
                        <span id="roles">
                            <?php
                            $block_role_ids_arr = $block_obj->getRoleIdsArr();
                            $roles_ids_arr = UsersUtils::getRolesIdsArr();

                            $container = Container::self();
                            $role_service = UsersServiceProvider::getRoleService($container);

                            foreach ($roles_ids_arr as $role_id) {
                                $role_obj = $role_service->getById($role_id);
                                ?>
                                <div class="checkbox">
                                    <label>
                                        <input value="<?php echo $role_id; ?>" type="checkbox" name="roles[]"<?php echo (in_array($role_id, $block_role_ids_arr) ? 'checked' : ''); ?>>
                                        <?php echo $role_obj->getName(); ?>
                                    </label>
                                </div>
                                <?php
                            }
                            ?>
                        </span>
                    </div>
                </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="format3docs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <p>Одна строка - один фильтр. Каждый фильтр должен начинаться с символов + или - и потом
                            пробела.</p>
                        <p>После символа + или - и пробела указывается маска адреса. + включает показ блока на этих
                            адресах, а - выключает.</p>
                        <p>Вот пример фильтра для блока, который показывается на всех страницах рубрики, кроме одной.</p>
                        <pre>
                        + rubrics_url
                        - rubrics_url/page
                        </pre>
                        <p>Маска - это регулярное выражение.</p>
                        <p>Т.е. "business/fcp" - это значит business/fcp может входить в адрес в любом месте.</p>
                        <p>"^/business/fcp" - это значит должно входить именно в начале адреса.</p>
                        <p>Главная страница - это "^/$".</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Видимость для страниц</div>
            <div class="panel-body">
                <div class="form-group">
                    <label>Набор включающих и исключающих фильтров. <a data-toggle="modal" href="#format3docs">Справка</a></label>
                    <textarea class="form-control" rows="10" name="pages" id="pages"><?php echo $block_obj->getPages(); ?></textarea>
                </div>
            </div>
        </div>

        <input class="btn btn-primary" type="submit" id="save-btn-js" value="Сохранить" />
        &nbsp;&nbsp;<button class="btn" id="regions-btn-js" type="button">Сохранить и выбрать регион</button>
    </form>

<script src="<?php echo Path::wrapSkifAssetsVersion('/libraries/ace/ace.js'); ?>" type="text/javascript" charset="utf-8"></script>

<script>
    var editor = ace.edit("editor");

    var form = $('#edit_form');

    editor.setTheme("ace/theme/crimson_editor");
    editor.getSession().setMode("ace/mode/php");
    editor.setValue(<?php echo $block_body_for_js = json_encode($block_obj->getBody()); ?>, 1);
    editor.getSession().setUseWrapMode(true);
    editor.$blockScrolling = Infinity;
    editor.blur();
    editor.focus();

    $('#save-btn-js').on('click', function() {
        $('#body').val(editor.getValue());
        $('#_redirect_to_on_success').val('');

        form.submit();
    });

    $('#regions-btn-js').on('click', function() {
        $('#body').val(editor.getValue());
        $('#_redirect_to_on_success').val('<?php echo ControllerBlocks::getRegionsListUrl($block_id); ?>');

        form.submit();
    });
</script>
