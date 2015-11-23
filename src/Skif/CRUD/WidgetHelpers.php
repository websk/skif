<?php

namespace Skif\CRUD;


class WidgetHelpers {

    public static function sortTermIdsArrByTitle($tids_arr){

        if(!is_array($tids_arr)){
            return array();
        }

        if(count($tids_arr) == 1){
            return $tids_arr;
        }

        $terms_to_sort_arr = array();

        foreach($tids_arr as $tid){
            $term_obj = \Skif\Term\TermFactory::getTermObj($tid);
            \Skif\Utils::assert($term_obj);
            $terms_to_sort_arr[$tid] = $term_obj->getTitle();
        }
        asort($terms_to_sort_arr);
        $sorted_ids_arr = array_keys($terms_to_sort_arr);

        return $sorted_ids_arr;

    }

    /**
     * Рисует путь от указанного tid до высшего родителя. Список разделен символом "/"
     *
     * @param $current_tid
     * @return string
     */
    public static function getParentsPathStringForTid($current_tid){

        if(!is_numeric($current_tid)){
            throw new Exception('No tid');
        }

        $current_term_obj = \Skif\Term\TermFactory::getTermObj($current_tid);

        $parent_tids_arr = \Skif\Term\TermHelper::getAllParentsForTid($current_tid);
        $parent_tids_sorted_arr = array_reverse($parent_tids_arr);
        $parent_terms_title_string = '';

        foreach($parent_tids_sorted_arr as $parent_tid){
            $parent_term_obj = \Skif\Term\TermFactory::getTermObj($parent_tid);
            $parent_terms_title_string .= $parent_term_obj->getTitle() . ' / ';
        }

        $parent_terms_title_string .= $current_term_obj->getTitle();

        return $parent_terms_title_string;

    }

} 