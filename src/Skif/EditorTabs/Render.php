<?php

namespace Skif\EditorTabs;

class Render
{

    static public function renderForObj($obj)
    {
        if (!$obj){
            return '';
        }

        $tabs_html = '';

        if ($obj instanceof \Skif\EditorTabs\InterfaceEditorTabs) {
            $tabs_obj_arr = $obj->getEditorTabsArr();

            $tabs_html .= '<ul class="nav nav-tabs" role="tablist">';

            foreach ($tabs_obj_arr as $tab_obj) {
                $tab_title = $tab_obj->getTitle();
                $tab_pathname = $tab_obj->getUrl();

                $li_class = '';
                if ($tab_pathname == \Skif\UrlManager::getUriNoQueryString()) {
                    $li_class .= ' active ';
                }

                $tabs_html .= '<li class="' . $li_class . '"><a ';
                if ($tab_obj->getTarget() != ''){
                    $tabs_html .= ' target="' . $tab_obj->getTarget() . '" ';
                }
                $tabs_html .= ' href="' . $tab_pathname . '">' . $tab_title . '</a></li>';
            }

            $tabs_html .= '</ul>';
        }

        return $tabs_html;
    }
} 