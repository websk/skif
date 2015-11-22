<?php
/**
 * @var $content_type_id
 */

$content_type_obj = \Skif\Content\ContentType::factory($content_type_id);
?>
<p><a href="<?php echo \Skif\Content\RubricController::getRubricsListUrlByContentType($content_type_obj->getType());?>/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить рубрику</a></p>
<p></p>
<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-6 col-sm-6 col-xs-6">
            <col class="col-md-2 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <?php
        $rubric_ids_arr = $content_type_obj->getRubricIdsArr();
        foreach ($rubric_ids_arr as $rubric_id) {
            $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);
            ?>
            <tr>
                <td><?php echo $rubric_obj->getId(); ?></td>
                <td>
                    <a href="<?php echo $rubric_obj->getEditorUrl(); ?>"><?php echo $rubric_obj->getName(); ?></a>
                </td>
                <td class="hidden-sm hidden-xs">
                    <?php echo $rubric_obj->getComment(); ?>
                </td>
                <td align="right">
                    <a href="<?php echo $rubric_obj->getEditorUrl(); ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="<?php echo $rubric_obj->getUrl(); ?>" target="_blank" title="Просмотр" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-external-link fa-lg text-info fa-fw"></span>
                    </a>
                    <a href="<?php echo $rubric_obj->getDeleteUrl(); ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                    </a>
                </td>
            </tr>
            <?
        }
        ?>
    </table>

