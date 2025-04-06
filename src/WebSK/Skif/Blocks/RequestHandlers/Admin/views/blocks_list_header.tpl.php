<?php
/**
 * @var int $current_template_id
 * @var string $search_value
 * @var TemplateService $template_service
 */

use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSearchHandler;
use WebSK\Skif\Content\TemplateService;
use WebSK\Slim\Router;
?>

<script type="text/javascript">
    function change_template() {
        var templates = document.getElementById('templates');
        template_id = templates.options[templates.selectedIndex].value;
        self.location = "/admin/blocks/change_template/" + template_id;
    }
</script>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <select name="templates" id="templates" onchange="change_template()" class="form-control">';
                        <?php
                        $templates_ids_arr = $template_service->getAllIdsArrByIdAsc();

                        foreach ($templates_ids_arr as $template_id) {
                            $template_obj = $template_service->getById($template_id);

                            ?>
                            <option value="<?php echo $template_id; ?>"<?php echo (($template_id == $current_template_id) ? ' selected' : '') ?>><?php echo $template_obj->getTitle(); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <form role="form" class="form-inline" action="<?php echo Router::urlFor(BlockSearchHandler::class); ?>">
                    <div class="form-group">
                        <label class="sr-only">Поиск</label>
                        <input class="form-control" type="text" value="<?php echo $search_value; ?>" name="search" id="search" placeholder="Поиск по содержимому">
                    </div>
                    <button type="submit" class="btn btn-default" title="Поиск по тексту блоков">
                        <span class="glyphicon glyphicon-search"></span> Искать
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/blocks/add" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить блок</a>
</p>

