<?php
/**
 * @var $site_menu_id
 * @var $site_menu_item_id
 * @var $site_menu_parent_item_id
 */

use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\SiteMenu\SiteMenuItem;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

$container = Container::self();

if ($site_menu_item_id == 'new') {
    $site_menu_item_obj = new SiteMenuItem();
    $site_menu_item_obj->setIsPublished(true);
} else {
    $site_menu_item_obj = SiteMenuItem::factory($site_menu_item_id);
}
?>
<script type="text/javascript">
    $().ready(function () {
        $("#site_menu_item_edit_form").validate({
            ignore: ":hidden",
            rules: {
                name: "required"
            },
            messages: {
                name: "Это поле обязательно для заполнения"
            }
        });
        $("#content_title").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo Router::pathFor(ContentRoutes::ROUTE_NAME_ADMIN_CONTENT_LIST_AUTOCOMPLETE); ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            delay: 10,
            minLength: 2,
            select: function(event, ui) {
                $("#content_id").val(ui.item.id);
                $("#url").val(ui.item.url).attr('disabled','disabled');
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append('<span class="ui-menu-item-type">' + item.type + '</span>' + item.label)
                .appendTo(ul);
        };
    })
</script>

<?php
$content_title = '';
$url = $site_menu_item_obj->getUrl();
if ($site_menu_item_obj->getContentId()) {
    $content_service = ContentServiceProvider::getContentService($container);

    $content_obj = $content_service->getById($site_menu_item_obj->getContentId());

    $content_title = $content_obj->getTitle();
    $url = $content_obj->getUrl();
}
?>

<form class="form-horizontal" action="/admin/site_menu/<?php echo $site_menu_id; ?>/item/save/<?php echo $site_menu_item_id; ?>" id="site_menu_item_edit_form" method="post">
    <div class="form-group">
        <label for="name" class="col-md-2 control-label">Название</label>
        <div class="col-md-10">
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $site_menu_item_obj->getName(); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="content_title" class="col-md-2 control-label">Материал</label>
        <div class="col-md-10">
            <input type="text" class="form-control" id="content_title" name="content_title" value="<?php echo $content_title; ?>">
            <input type="hidden" id="content_id" name="content_id" value="<?= $site_menu_item_obj->getContentId() ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="weight" class="col-md-2 control-label">Вес</label>
        <div class="col-md-10">
            <input type="text" class="form-control" id="weight" name="weight" value="<?php echo $site_menu_item_obj->getWeight(); ?>" disabled>
        </div>
    </div>
    <div class="form-group">
        <label for="parent_id" class="col-md-2 control-label">Родитель</label>
        <div class="col-md-10">
            <input type="text" class="form-control" id="parent_id" name="parent_id" value="<?php echo $site_menu_parent_item_id; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="url" class="col-md-2 control-label">Ссылка</label>
        <div class="col-md-10">
            <input type="text" class="form-control" id="url" name="url" value="<?php echo $url; ?>"<?php echo ($site_menu_item_obj->getContentId() ? ' disabled' : ''); ?>>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="is_published"<?php echo ($site_menu_item_obj->isPublished() ? ' checked' : ''); ?> value="1">
                    Показывать
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <input type="submit" class="btn btn-primary" value="Сохранить изменения">
        </div>
    </div>

</form>