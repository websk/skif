<?php

namespace Skif\Tree;

class Controller
{
    protected $requested_model_class_name;

    public function currentOperatorHasPermissionsToEditModelByClass($model_class_name)
    {
        if (!property_exists($model_class_name, 'operator_permissions_arr_required_to_edit')) {
            return false;
        }

        return \Skif\Operator\OperatorHelper::currentOperatorHasAnyOfPermissions($model_class_name::$operator_permissions_arr_required_to_edit);
    }

    public function setRequestedModelClassName($requested_model_class_name)
    {
        $this->requested_model_class_name = $requested_model_class_name;
    }

    public function getRequestedModelClassName()
    {
        return $this->requested_model_class_name;
    }

    public function renderTreeForClassName($model_class_name)
    {
        $root_menu_ids = $model_class_name::getRootItemsIdsArr();

        return \SkifRu\Render\Render::callLocaltemplate('templates/menu_tree.tpl.php', array(
            "model_class_name" => $model_class_name,
            "item_ids" => $root_menu_ids,
            "level" => 1
        ));

    }

    public static function renderSubtreeForClassName($model_class_name, $parent_id, $level)
    {
        $menu_obj = $model_class_name::factory($parent_id);
        \Skif\Helpers::assert($menu_obj);

        $child_menu_ids = $menu_obj->getChildrenIdsArr();
        $level++;
        return \SkifRu\Render\Render::callLocaltemplate('templates/menu_tree.tpl.php', array(
            'model_class_name' => $model_class_name,
            "item_ids" => $child_menu_ids,
            "level" => $level
        ));

    }

    public function viewAction($model_class_name)
    {
        \Skif\Helpers::assert($model_class_name);
        $this->setRequestedModelClassName($model_class_name);

        \Skif\Helpers::exit403If(!self::currentOperatorHasPermissionsToEditModelByClass($model_class_name));
        \Skif\Tree\Item::exceptionIfClassNotCompatibleWithTree($model_class_name);

        $content_html = \SkifRu\Render\Render::callLocaltemplate('templates/menu_tree_container.tpl.php', array('model_class_name' => $model_class_name));

        echo \Skif\Render::template2('Skif/Admin/templates/layout.tpl.php', array(
            'title' => "Дерево",
            'content' => $content_html
        ));
    }

    public static function setParentAction()
    {
        \Skif\Helpers::exit404If(!array_key_exists('model_class_name', $_POST));
        \Skif\Helpers::exit404If(!array_key_exists('parent_id', $_POST));
        \Skif\Helpers::exit404If(!array_key_exists('menu_id', $_POST));

        $model_class_name = $_POST['model_class_name'];
        $parent_menu_id = (int)$_POST['parent_id'];
        $menu_id = (int)$_POST['menu_id'];

        \Skif\Helpers::exit403If(!self::currentOperatorHasPermissionsToEditModelByClass($model_class_name));
        \Skif\Tree\Item::exceptionIfClassNotCompatibleWithTree($model_class_name);

        $menu_obj = $model_class_name::factory($menu_id);
        \Skif\Helpers::assert($menu_obj);

        $menu_obj->setParentId($parent_menu_id);

        //$menu_obj->save();

        self::setWeightForMenuItemAndUpdateSiblings($model_class_name, $menu_obj->getId(), 0);

        $output_obj = \Skif\Tree\Helpers::getItemObjectForOutput($model_class_name, $menu_obj->getId());

        $json_arr = array("status" => "success", "menu_obj" => $output_obj);
        echo json_encode($json_arr);
    }


    public function setWeightAction()
    {
        \Skif\Helpers::exit404If(!array_key_exists('model_class_name', $_POST));
        \Skif\Helpers::exit404If(!array_key_exists('target_menu_id', $_POST));
        \Skif\Helpers::exit404If(!array_key_exists('menu_id', $_POST));
        \Skif\Helpers::exit404If(!array_key_exists('direction', $_POST));

        $model_class_name = $_POST['model_class_name'];
        $target_menu_id = (int)$_POST['target_menu_id'];
        $menu_id = (int)$_POST['menu_id'];

        \Skif\Helpers::exit403If(!self::currentOperatorHasPermissionsToEditModelByClass($model_class_name));
        \Skif\Tree\Item::exceptionIfClassNotCompatibleWithTree($model_class_name);

        $menu_obj = $model_class_name::factory($menu_id);
        \Skif\Helpers::assert($menu_obj);

        $target_menu_obj = $model_class_name::factory($target_menu_id);
        \Skif\Helpers::assert($target_menu_obj);

        $new_weight = $target_menu_obj->getWeight();

        if ($_POST['direction'] == "down") {
            $new_weight = $target_menu_obj->getWeight() + 1;
        }

        if ($menu_obj->getParentId() != $target_menu_obj->getParentId()) {
            $new_parent_id = $target_menu_obj->getParentId();
            $menu_obj->setParentId($new_parent_id);
            //$menu_obj->save();
        }

        self::setWeightForMenuItemAndUpdateSiblings($model_class_name, $menu_obj->getId(), $new_weight);

        $output_obj = \Skif\Tree\Helpers::getItemObjectForOutput($model_class_name, $menu_obj->getId());

        $json_arr = array("status" => "success", "menu_obj" => $output_obj);
        echo json_encode($json_arr);

    }

    public static function setWeightForMenuItemAndUpdateSiblings($model_class_name, $menu_id, $new_weight)
    {
        \Skif\Helpers::assert($menu_id);

        $menu_obj = $model_class_name::factory($menu_id);
        \Skif\Helpers::assert($menu_obj);

        if ($menu_obj->getParentId() == 0) {
            $menu_item_ids_arr = $model_class_name::getRootItemsIdsArr();
        } else {
            $parent_menu_obj = $model_class_name::factory($menu_obj->getParentId());
            \Skif\Helpers::assert($parent_menu_obj);
            $menu_item_ids_arr = $parent_menu_obj->getChildrenIdsArr();
        }

        foreach ($menu_item_ids_arr as $menu_item_id) {
            $sibling_menu_obj = $model_class_name::factory($menu_item_id);
            \Skif\Helpers::assert($sibling_menu_obj);

            $weight = $sibling_menu_obj->getWeight();

            if ($weight < $new_weight) {
                continue;
            }
            $sibling_menu_obj->setWeight($weight + 1);
            //$sibling_menu_obj->save();
        }

        $menu_obj->setWeight($new_weight);

        //$menu_obj->save();
    }

    public static function addNewAction()
    {
        \Skif\Helpers::exit404If(!array_key_exists('model_class_name', $_POST));

        $model_class_name = $_POST['model_class_name'];

        \Skif\Helpers::exit403If(!self::currentOperatorHasPermissionsToEditModelByClass($model_class_name));
        \Skif\Tree\Item::exceptionIfClassNotCompatibleWithTree($model_class_name);

        $new_menu_obj = new $model_class_name;
        \Skif\Helpers::assert($new_menu_obj);
        $new_menu_obj->save(); // to generate id

        $new_id = $new_menu_obj->getId();
        //$new_menu_obj->setText("Новое меню " . $new_id);
        //$new_menu_obj->setIsPublished(0);
        //$new_menu_obj->save();

        self::setWeightForMenuItemAndUpdateSiblings($model_class_name, $new_menu_obj->getId(), 0);

        $additional_buttons = '';

        if (method_exists($new_menu_obj, 'getTreeAdditionalButtons')) {
            foreach ($new_menu_obj->getTreeAdditionalButtons() as $button) {
                $additional_buttons .= $button;
            }
        }

        $output_obj = \Skif\Tree\Helpers::getItemObjectForOutput($model_class_name, $new_menu_obj->getId());

        $obj_class = get_class($new_menu_obj);
        $json_arr = array(
            "status" => "success",
            "menu_obj" => $output_obj,
            "additional_buttons" => $additional_buttons,
            "obj_class" =>  urlencode($obj_class)
        );

        echo json_encode($json_arr);
    }

    public function removeAction()
    {

        \Skif\Helpers::exit404If(!array_key_exists('model_class_name', $_POST));
        \Skif\Helpers::exit404If(!array_key_exists('menu_id', $_POST));

        $model_class_name = $_POST['model_class_name'];
        $menu_id = (int)$_POST['menu_id'];

        \Skif\Helpers::exit403If(!self::currentOperatorHasPermissionsToEditModelByClass($model_class_name));
        \Skif\Tree\Item::exceptionIfClassNotCompatibleWithTree($model_class_name);

        $menu_obj = $model_class_name::factory($menu_id);
        \Skif\Helpers::assert($menu_obj);

        if ($menu_obj->getChildrenIdsArr()) {
            $json_arr = array("status" => "error");
            echo json_encode($json_arr);
            return;
        }

        $menu_obj->delete();

        $json_arr = array("status" => "success");
        echo json_encode($json_arr);
    }

}