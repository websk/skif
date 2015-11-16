<?php
/**
 * @var $site_menu_id
 * @var $parent_id
 */
?>
<p><a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/edit/new?site_menu_parent_item_id=<?php echo $parent_id; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить новый пункт меню</a></p>
<p></p>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1">
            <col class="col-md-8">
            <col class="col-md-1">
            <col class="col-md-1">
        </colgroup>
<?php
$site_menu_item_ids_arr = \Skif\SiteMenu\SiteMenuUtils::getSiteMenuItemIdsArr($site_menu_id, $parent_id);

$counter_item = 0;
foreach ($site_menu_item_ids_arr as $site_menu_item_id) {
    $site_menu_item_obj = \Skif\SiteMenu\SiteMenuItem::factory($site_menu_item_id);
    ?>
    <tr>
        <td><?php echo $site_menu_item_id; ?></td>
        <td>
            <?php
            if ($site_menu_item_obj->getChildrenIdsArr()) {
                echo '<a href="/admin/site_menu/' . $site_menu_id . '/items/list/' . $site_menu_item_id . '">' . $site_menu_item_obj->getName() . '</a>';
            } else {
                echo $site_menu_item_obj->getName() . ' <a href="/admin/site_menu/' . $site_menu_id . '/item/edit/new?site_menu_parent_item_id=' . $site_menu_item_id .'"><span class="glyphicon glyphicon-plus" title="Добавить вложенный пункт"></span></a>';
            }
            ?>
        </td>
        <td align="right">
            <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/edit/<?php echo $site_menu_item_id; ?>"><span class="glyphicon glyphicon-edit text-warning" title="Редактировать"></span></a>&nbsp;
            <?php
            if ($site_menu_item_obj->getUrl()) {
                ?>
                <a href="<?php echo $site_menu_item_obj->getUrl(); ?>" target="_blank"><span class="glyphicon glyphicon-new-window" title="Посмотреть"></span></a>&nbsp;
            <?php
            }
            ?>
        </td>
        <td align="right">
            <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/items/list_for_move/<?php echo $parent_id; ?>?move_item_id=<?php echo $site_menu_item_id; ?>"><span class="glyphicon glyphicon-move text-muted" title="Переместить"></span></a>
            <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/delete/<?php echo $site_menu_item_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')"><span class="glyphicon glyphicon-remove text-danger" title="Удалить"></span></a>
        </td>
    </tr>
<?php
    $counter_item++;
}
?>
    </table>
</div>
