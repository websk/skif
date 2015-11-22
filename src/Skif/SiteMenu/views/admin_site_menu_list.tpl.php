<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/site_menu/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить меню</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-8 col-sm-6 col-xs-6">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
<?php
$site_menu_ids_arr = \Skif\SiteMenu\SiteMenuUtils::getSiteMenuIdsArr();

foreach ($site_menu_ids_arr as $site_menu_id) {
    $site_menu_obj = \Skif\SiteMenu\SiteMenu::factory($site_menu_id);
    ?>
    <tr>
        <td><?php echo $site_menu_id; ?></td>
        <td><a href="/admin/site_menu/<?php echo $site_menu_id; ?>/items/list/0"><?php echo $site_menu_obj->getName(); ?></a></td>
        <td align="right">
            <a href="/admin/site_menu/edit/<?php echo $site_menu_id; ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
            </a>
            <a href="/admin/site_menu/delete/<?php echo $site_menu_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
                <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
            </a>
        </td>
    </tr>
<?php
}
?>
    </table>
</div>
