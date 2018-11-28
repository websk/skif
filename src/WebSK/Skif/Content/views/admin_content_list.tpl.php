<?php
/**
 * @var $content_type
 */

use WebSK\Skif\Pager;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\ContentUtils;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\RubricController;

$content_type_obj = ContentType::factoryByFieldsArr(array('type' => $content_type));

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$requested_rubric_id = array_key_exists('rubric_id', $_GET) ? $_GET['rubric_id'] : 0;

$limit_to_page = 100;

if ($requested_rubric_id) {
    $contents_ids_arr = ContentUtils::getContentsIdsArrByRubricId($requested_rubric_id, $limit_to_page, $page);
    $count_all_articles = ContentUtils::getCountContentsByRubricId($requested_rubric_id);
} else {
    $contents_ids_arr = ContentUtils::getContentsIdsArrByType($content_type, $limit_to_page, $page);
    $count_all_articles = ContentUtils::getCountContentsByType($content_type);
}
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <form action="/admin/content/<?php echo $content_type; ?>" class="form-inline">
                    <div class="form-group">
                        <label>Рубрика</label>

                        <select name="rubric_id" class="form-control">
                            <option value="0">Все</option>
                            <?php
                            $rubric_ids_arr = $content_type_obj->getRubricIdsArr();
                            foreach ($rubric_ids_arr as $rubric_id) {
                                $rubric_obj = Rubric::factory($rubric_id);

                                echo '<option value="' . $rubric_id . '" ' . ($rubric_id == $requested_rubric_id ? 'selected' : '') . '>' . $rubric_obj->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Выбрать" class="btn btn-default">
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <a href="<?php echo RubricController::getRubricsListUrlByContentType($content_type);?>" class="btn btn-outline btn-info">
                    <span class="glyphicon glyphicon-wrench"></span> Редактировать рубрики
                </a>
            </div>
        </div>
    </div>
</div>


<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/content/<?php echo $content_type; ?>/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить материал</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-6 col-sm-6 col-xs-6">
            <col class="col-md-2 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <tbody>
<?php
foreach ($contents_ids_arr as $content_id) {
    $content_obj = Content::factory($content_id);
    ?>
    <tr>
        <td><?php echo $content_obj->getId(); ?></td>
        <td>
            <a href="/admin/content/<?php echo $content_type; ?>/edit/<?php echo $content_id; ?>"><?php echo $content_obj->getTitle(); ?></a>
            <?php
            $rubric_ids_arr = $content_obj->getRubricIdsArr();

            foreach ($rubric_ids_arr as $rubric_id) {
                $rubric_obj = Rubric::factory($rubric_id);
                ?>
                <span class="badge"><?php echo $rubric_obj->getName(); ?></span>
                <?php
            }
            ?>
        </td>
        <td class="hidden-xs hidden-sm text-muted"><?php echo $content_obj->getCreatedAt(); ?></td>
        <td align="right">
            <a href="/admin/content/<?php echo $content_type; ?>/edit/<?php echo $content_id; ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
            </a>
            <a href="<?php echo $content_obj->getUrl(); ?>" target="_blank" title="Просмотр" class="btn btn-outline btn-default btn-sm">
                <span class="fa fa-external-link fa-lg text-info fa-fw"></span>
            </a>
            <a href="/admin/content/<?php echo $content_type; ?>/delete/<?php echo $content_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
                <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
            </a>
        </td>
    </tr>
    <?php
}
?>
        </tbody>
    </table>
</div>
<?php
echo Pager::renderPagination($page, $count_all_articles, $limit_to_page);
?>
