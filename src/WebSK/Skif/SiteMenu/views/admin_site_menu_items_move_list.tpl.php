<?php
/**
 * @var $site_menu_id
 * @var $parent_id
 * @var $move_item_id
 */

use WebSK\Skif\SiteMenu\SiteMenuItem;
use WebSK\Skif\SiteMenu\SiteMenuUtils;

$site_menu_move_item_obj = SiteMenuItem::factory($move_item_id);
?>

<p class="alert alert-info">Выберите, после или внутри какого пункта расположить выбранный пункт
    меню &laquo;<b><?php echo $site_menu_move_item_obj->getName(); ?></b>&raquo;

     / <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/items/list/<?php echo $site_menu_move_item_obj->getParentId() ?>" class="btn btn-default btn-sm">Отменить</a>
</p>

<p></p>
<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-8 col-sm-6 col-xs-6">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <?php
        $site_menu_item_ids_arr = SiteMenuUtils::getSiteMenuItemIdsArr($site_menu_id, $parent_id);
        ?>
        <tr>
            <td>
                <?php
                if ($parent_id) {
                    $site_menu_parent_item_obj = SiteMenuItem::factory($parent_id);
                    ?>
                    <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/items/list_for_move/<?php echo $site_menu_parent_item_obj->getParentId() ?>?move_item_id=<?php echo $move_item_id; ?>" title="Перейти на уровень выше">
                        <span class="fa fa-long-arrow-left fa-lg text-muted"></span>
                    </a>
                <?php
                }
                ?>
            </td>
            <td colspan="2">
                <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/move/<?php echo $move_item_id; ?>?destination_parent_item_id=<?php echo $parent_id; ?>">
                    Сделать первым
                </a>
            </td>
        </tr>
        <?php

        foreach ($site_menu_item_ids_arr as $site_menu_item_id) {
            $site_menu_item_obj = SiteMenuItem::factory($site_menu_item_id);

            if ($site_menu_item_id == $move_item_id) {
                continue;
            }

            $children_ids_arr = $site_menu_item_obj->getChildrenIdsArr();
            ?>
            <tr>
                <td><?php echo $site_menu_item_id; ?></td>
                <td>
                    <?php
                    if ($children_ids_arr) {
                        echo '<a href="/admin/site_menu/' . $site_menu_id . '/items/list_for_move/' . $site_menu_item_id . '?move_item_id=' . $move_item_id . '">' . $site_menu_item_obj->getName() . '</a>';
                    } else {
                        echo $site_menu_item_obj->getName();
                        ?>
                        <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/move/<?php echo $move_item_id; ?>?destination_parent_item_id=<?php echo $site_menu_item_id; ?>" title="Сделать вложенным пунктом" class="btn btn-outline btn-default btn-sm">
                            <span class="fa fa-hand-o-left fa-lg text-warning"></span>
                        </a>
                    <?php
                    }
                    ?>
                </td>
                <td align="right">
                    <a href="/admin/site_menu/<?php echo $site_menu_id; ?>/item/move/<?php echo $move_item_id; ?>?destination_parent_item_id=<?php echo $parent_id; ?>&destination_item_id=<?php echo $site_menu_item_id; ?>" title="Расположить после" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-hand-o-down fa-lg text-primary"></span>
                    </a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
