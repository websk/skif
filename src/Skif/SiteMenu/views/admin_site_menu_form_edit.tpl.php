<?php
/**
 * @var $site_menu_id
 */

if ($site_menu_id == 'new') {
    $site_menu_obj = new \Skif\SiteMenu\SiteMenu();
} else {
    $site_menu_obj = \Skif\SiteMenu\SiteMenu::factory($site_menu_id);
}
?>
<script type="text/javascript">
    $().ready(function () {
        $("#site_menu_edit_form").validate({
            ignore: ":hidden",
            rules: {
                name: "required"
            },
            messages: {
                name: "Это поле обязательно для заполнения"
            }
        });
    })
</script>

<form action="/admin/site_menu/save/<?php echo $site_menu_id; ?>" id="site_menu_edit_form" method="post">
    <div class="form-group">
        <label for="name" class="control-label">Название</label>

        <div>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $site_menu_obj->getName(); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="url" class="control-label">URL</label>

        <div>
            <input type="text" class="form-control" id="url" name="url" value="<?php echo $site_menu_obj->getUrl(); ?>">
        </div>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Сохранить изменения">
    </div>

</form>