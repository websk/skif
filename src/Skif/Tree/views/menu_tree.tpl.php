<?php

/**
 * @variable $model_class_name
 * @variable $item_ids
 * @variable $level
 */

foreach ($item_ids as $root_menu_id) {
    $menu_obj = $model_class_name::factory($root_menu_id);
    \Skif\Helpers::assert($menu_obj);

    $menu_edit_url = \Skif\CRUD\ControllerCRUD::getEditUrlForObj($menu_obj);

    // TODO: check gettitle support
    $menu_text = htmlspecialchars($menu_obj->getTitle());

    $matches = array();
    preg_match('~(\S)~', $menu_text, $matches);

    if (!$matches) {
        $menu_text = "БЕЗ ТЕКСТА";
    }

    $published_class = "unpublished";

    ?>
    <li id="menu-tree-<?= $menu_obj->getId() ?>"
        data-parent-menu="<?= $menu_obj->getParentId() ?>"
        data-menu-id="<?= $menu_obj->getId() ?>">

        <?php if (!$menu_obj->getParentId()) {
            echo "<strong>";
        } ?>
        <a href="<?= $menu_edit_url ?>" class="menu-edit" target="_blank"><?= $menu_text ?></a>
        <?php if (!$menu_obj->getParentId()) {
            echo "</strong>";
        } ?>
        <small>
            <?php
            $tree_additional_buttons = array();

            if (method_exists($menu_obj, 'getTreeAdditionalButtons')) {
                $tree_additional_buttons = $menu_obj->getTreeAdditionalButtons();
            }


            if (count($tree_additional_buttons) > 0) {
                foreach ($tree_additional_buttons as $tree_additional_button) {
                    echo $tree_additional_button;
                }
            }


            ?>

            <a href="#"
               class="remove-menu-item glyphicon glyphicon-remove"
               title="Удалить"></a>

                <span class="actions">

                <a href="#"
                   class="move-up-menu-item glyphicon glyphicon-arrow-up"
                   title="Переместить выше" style="display: none"></a>

                <a href="#"
                   class="move-down-menu-item glyphicon glyphicon-arrow-down"
                   title="Переместить ниже" style="display: none"></a>

                <a href="#"
                   class="change-parent-menu-item glyphicon glyphicon-arrow-right"
                   title="Сделать родителем" style="display: none"></a>

                <a href="#"
                   class="move-menu-item glyphicon glyphicon-move"
                   title="Переместить"></a>

                <a href="#" class="cancel-action" style="display: none"><strong>Отменить</strong></a>

                </span>
        </small>
        <ul style=" list-style-type: none;">
            <?php

            $children_ids_arr = $menu_obj->getChildrenIdsArr();

            if (!empty($children_ids_arr)) {
                echo \Skif\Tree\Controller::renderSubtreeForClassName($model_class_name, $menu_obj->getId(), $level);
            }

            ?>
        </ul>
    </li>
<?php
}
?>
