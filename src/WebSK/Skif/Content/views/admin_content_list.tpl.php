<?php
/**
 * @var $content_type
 */

use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
use WebSK\Skif\Pager;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

$container = Container::self();

$content_service = ContentServiceProvider::getContentService($container);
$content_type_service = ContentServiceProvider::getContentTypeService($container);
$rubric_service = ContentServiceProvider::getRubricService($container);
$content_rubric_service = ContentServiceProvider::getContentRubricService($container);

$content_type_obj = $content_type_service->getByType($content_type);

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$requested_rubric_id = array_key_exists('rubric_id', $_GET) ? $_GET['rubric_id'] : 0;

$limit_to_page = 100;

if ($requested_rubric_id) {
    $contents_ids_arr = $content_rubric_service->getContentIdsArrByRubricId($requested_rubric_id, $limit_to_page, $page);
    $count_all_articles = $content_rubric_service->getCountContentsByRubricId($requested_rubric_id);
} else {
    $contents_ids_arr = $content_service->getIdsArrByType($content_type, $limit_to_page, $page);
    $count_all_articles = $content_service->getCountContentsByType($content_type);
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
                            $rubric_ids_arr = $rubric_service->getIdsArrByContentTypeId($content_type_obj->getId());
                            foreach ($rubric_ids_arr as $rubric_id) {
                                $rubric_obj = $rubric_service->getById($rubric_id);

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
                <a href="<?php echo Router::pathFor(ContentRoutes::ROUTE_NAME_ADMIN_RUBRIC_LIST, ['content_type' => $content_type]);?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-wrench"></span> Редактировать рубрики
                </a>
            </div>
        </div>
    </div>
</div>


<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/content/<?php echo $content_type; ?>/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить материал</a>
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
    $content_obj = $content_service->getById($content_id);
    ?>
    <tr>
        <td><?php echo $content_obj->getId(); ?></td>
        <td>
            <a href="<?php echo Router::pathFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>"><?php echo $content_obj->getTitle(); ?></a>
            <?php
            $rubric_ids_arr = $content_rubric_service->getRubricIdsArrByContentId($content_obj->getId());

            foreach ($rubric_ids_arr as $rubric_id) {
                $rubric_obj = $rubric_service->getById($rubric_id);
                ?>
                <span class="badge"><?php echo $rubric_obj->getName(); ?></span>
                <?php
            }
            ?>
        </td>
        <td class="hidden-xs hidden-sm text-muted"><?php echo $content_obj->getCreatedAtTs(); ?></td>
        <td align="right">
            <a href="<?php echo Router::pathFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>" title="Редактировать" class="btn btn-default btn-sm">
                <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
            </a>
            <a href="<?php echo $content_obj->getUrl(); ?>" target="_blank" title="Просмотр" class="btn btn-default btn-sm">
                <span class="fa fa-external-link fa-lg text-info fa-fw"></span>
            </a>
            <a href="/admin/content/<?php echo $content_type; ?>/delete/<?php echo $content_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-default btn-sm">
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
