<?php

namespace Skif;


class CountryController
{
    /**
     * Автокомплит для поля "страна"
     */
    public static function CountriesAutoCompleteAction()
    {
        $term = array_key_exists('term', $_REQUEST) ? trim($_REQUEST['term']) : '';

        $query_param_arr = array($term .'%');

        $query = "SELECT id, name FROM lands WHERE name LIKE ?";
        $countries_arr = \Skif\DB\DBWrapper::readObjects($query, $query_param_arr);

        $output_arr = array();
        foreach ($countries_arr as $country_obj) {
            $output_arr[] = array(
                'id' => $country_obj->id,
                'label' => $country_obj->name,
                'value' => $country_obj->name,
            );
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output_arr);
    }

} 