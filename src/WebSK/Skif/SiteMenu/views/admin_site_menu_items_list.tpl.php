<?php
/**
 * @var int $site_menu_id
 * @var int $parent_id
 */

use WebSK\Skif\SiteMenu\SiteMenuServiceProvider;
use WebSK\Slim\Container;

$container = Container::self();
$site_menu_item_service = SiteMenuServiceProvider::getSiteMenuItemService($container);
?>
<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/edit/new?site_menu_parent_item_id=<?php echo $parent_id; ?>" class="btn btn-primary">
        <span class="glyphicon glyphicon-plus"></span> Добавить пункт меню</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-8 col-sm-6 col-xs-6">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
<?php
$site_menu_item_ids_arr = $site_menu_item_service->getIdsArrBySiteMenuId($site_menu_id, $parent_id);

$counter_item = 0;
foreach ($site_menu_item_ids_arr as $site_menu_item_id) {
    $site_menu_item_obj = $site_menu_item_service->getById($site_menu_item_id);
    ?>
    <tr>
        <td><?php echo $site_menu_item_id; ?></td>
        <td>
            <?php
            if ($site_menu_item_service->getChildrenIdsArr()) {
                echo '<a href="/admin/site_menu/' . $site_menu_id . '/items/list/' . $site_menu_item_id . '">' . $site_menu_item_obj->getName() . '</a>';
            } else {
                ?>
                <?php echo $site_menu_item_obj->getName(); ?>
                <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/edit/new?site_menu_parent_item_id=<?php echo $site_menu_item_id; ?>" title="Добавить вложенный пункт" class="btn btn-default btn-sm">
                    <span class="fa fa-hand-o-left fa-lg text-primary"></span>
                    <span class="fa fa-plus fa-lg text-primary"></span>
                </a>
            <?php
            }
            ?>
        </td>
        <td align="right">
            <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/edit/<?php echo $site_menu_item_id; ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
            </a>
            <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/items/list_for_move/<?php echo $parent_id; ?>?move_item_id=<?php echo $site_menu_item_id; ?>" title="Переместить" class="btn btn-default btn-sm">
                <span class="fa fa-arrows fa-lg text-muted fa-fw" title="Переместить"></span>
            </a>
            <?php
            if ($site_menu_item_obj->getUrl()) {
                ?>
                <a href="<?php echo $site_menu_item_obj->getUrl(); ?>" target="_blank" title="Просмотр" class="btn btn-outline btn-default btn-sm hidden-xs">
                    <span class="fa fa-external-link fa-lg text-info fa-fw"></span>
                </a>
                <?php
            }
            ?>
            <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/delete/<?php echo $site_menu_item_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-default btn-sm">
                <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
            </a>
        </td>
    </tr>
<?php
    $counter_item++;
}
?>
    </table>
</div>
