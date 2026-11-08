<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 * @var BlockRoleService $block_role_service
 * @var RoleService $role_service
 */

use WebSK\Auth\User\RoleService;
use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\BlockRoleService;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveContentHandler;
use WebSK\Skif\SkifPath;
use WebSK\Skif\Router;
use WebSK\Utils\Sanitize;
use WebSK\Views\PhpRender;

$block_obj = $block_service->getById($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    [
        'block_id' => $block_id,
        'block_service' => $block_service
    ]
);

$items = [];
?>
<form role="form" action="<?php echo Router::urlFor(BlockSaveContentHandler::class, ['block_id' => $block_id]); ?>" method="post" id="edit_form">
    <input type="hidden" value="" name="_redirect_to_on_success" id="_redirect_to_on_success"/>

    <div class="mb-3">
        <label for="title" class="form-label">Название</label>
        <input class="form-control" type="text" value="<?php echo Sanitize::sanitizeAttrValue($block_obj->getTitle()); ?>" name="title" id="title"/>

        <p class="form-text">Описание выводится в админке блоков.</p>
    </div>

    <style media="screen">
        #editor {
            height: 500px;
            font-size: 100%;
        }
    </style>

    <div class="mb-3">
        <label for="editor" class="form-label">Текст блока</label>
        <div id="editor"></div>
        <input type="hidden" value="" name="body" id="body"/>
    </div>

    <div class="mb-3">
        <label for="format" class="form-label">Формат ввода</label>
        <select class="form-select" name="format" id="format">
            <?php
            $formats_arr = Block::BLOCK_FORMATS_ARRAY;

            $current_format = $block_obj->getFormat();

            foreach ($formats_arr as $format_id => $format_name) {
                ?>
                <option value="<?php echo $format_id; ?>"<?php echo($current_format == $format_id ? ' selected="selected"' : '') ?>>
                    <?php echo $format_name; ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <a data-bs-toggle="collapse" href="#visibility_roles_panel">Видимость для ролей</a>
        </div>
        <div id="visibility_roles_panel" class="collapse">
            <div class="card-body">
                <div class="mb-3">
                        <span id="roles">
                            <?php
                            $block_role_ids_arr = $block_role_service->getRoleIdsByBlockId($block_id);

                            $roles_ids_arr = $role_service->getAllIdsArrByIdAsc();

                            foreach ($roles_ids_arr as $role_id) {
                                $role_obj = $role_service->getById($role_id);
                                ?>
                                <?php $role_input_id = 'role-' . $role_id; ?>
                                <div class="form-check">
                                    <input id="<?php echo $role_input_id; ?>" class="form-check-input" value="<?php echo $role_id; ?>" type="checkbox"
                                               name="roles[]"<?php echo(in_array($role_id,
                                            $block_role_ids_arr) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="<?php echo $role_input_id; ?>"><?php echo Sanitize::sanitizeTagContent($role_obj->getName()); ?></label>
                                </div>
                                <?php
                            }
                            ?>
                        </span>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="format3docs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
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

    <div class="card mb-3">
        <div class="card-header">Видимость для страниц</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="pages" class="form-label">Набор включающих и исключающих фильтров. <a data-bs-toggle="modal"
                                                                   href="#format3docs">Справка</a></label>
                <textarea class="form-control" rows="10" name="pages"
                          id="pages"><?php echo Sanitize::sanitizeTagContent($block_obj->getPages()); ?></textarea>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <input class="btn btn-primary" type="submit" id="save-btn-js" value="Сохранить"/>
    </div>
</form>

<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/ace/ace.js'); ?>" type="text/javascript"
        charset="utf-8"></script>

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

    $('#save-btn-js').on('click', function () {
        $('#body').val(editor.getValue());
        $('#_redirect_to_on_success').val('');

        form.submit();
    });
</script>
