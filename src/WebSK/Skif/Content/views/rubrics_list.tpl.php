<?php
/**
 * @var $content_type_id
 */

use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\RubricController;
use WebSK\Slim\Container;

$content_type_service = ContentServiceProvider::getContentTypeService(Container::self());

$content_type_obj = $content_type_service->getById($content_type_id);
?>
<p class="padding_top_10 padding_bottom_10">
    <a href="<?php echo RubricController::getRubricsListUrlByContentType($content_type_obj->getType());?>/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить рубрику</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-6 col-sm-6 col-xs-6">
            <col class="col-md-2 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <?php
        $rubric_ids_arr = Rubric::findIdsArrByContentTypeId($content_type_obj->getId());
        foreach ($rubric_ids_arr as $rubric_id) {
            $rubric_obj = Rubric::factory($rubric_id);
            ?>
            <tr>
                <td><?php echo $rubric_obj->getId(); ?></td>
                <td>
                    <a href="<?php echo RubricController::getEditorUrl($rubric_obj); ?>"><?php echo $rubric_obj->getName(); ?></a>
                </td>
                <td class="hidden-sm hidden-xs">
                    <?php echo $rubric_obj->getComment(); ?>
                </td>
                <td align="right">
                    <a href="<?php echo RubricController::getEditorUrl($rubric_obj); ?>" title="Редактировать" class="btn btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="<?php echo $rubric_obj->getUrl(); ?>" target="_blank" title="Просмотр" class="btn btn-default btn-sm">
                        <span class="fa fa-external-link fa-lg text-info fa-fw"></span>
                    </a>
                    <a href="<?php echo RubricController::getDeleteUrl($rubric_obj); ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-default btn-sm">
                        <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>

