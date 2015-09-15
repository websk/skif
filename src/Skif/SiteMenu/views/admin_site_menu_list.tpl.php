<p><a href="/admin/site_menu/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить новое меню</a></p>
<p></p>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1">
            <col class="col-md-9">
            <col class="col-md-1">
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
            <a href="/admin/site_menu/edit/<?php echo $site_menu_id; ?>"><span class="glyphicon glyphicon-edit" title="Редактировать"></span></a>
            <a href="/admin/site_menu/delete/<?php echo $site_menu_id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')"><span class="glyphicon glyphicon-remove" title="Удалить"></span></a>
        </td>
    </tr>
<?php
}
?>
    </table>
</div>
