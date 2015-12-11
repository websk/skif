<?php
/**
 * Created by PhpStorm.
 * User: lev
 * Date: 20.02.15
 * Time: 13:12
 */

namespace Skif\Tree;


class Helpers {

    public static function getItemObjectForOutput($model_class_name, $obj_id){

        $obj = $model_class_name::factory($obj_id);
        \Skif\Helpers::assert($obj);

        $output_obj = new \stdClass();

        $output_obj->id = $obj->getId();
        $output_obj->weight = $obj->getWeight();
        $output_obj->parent_id = $obj->getParentId();
        $output_obj->text = $obj->getName();

        return $output_obj;
    }

} 