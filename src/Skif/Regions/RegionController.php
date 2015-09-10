<?php

namespace Skif\Regions;

class RegionController
{

    public function importFromVKAction()
    {
        if (!\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            return;
        }

        $url = 'http://api.vk.com/method/database.getRegions?v=5.5&need_all=1&offset=0&count=1000&country_id=1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        $json_result_arr = json_decode($result, true);


        if (empty($json_result_arr['response']['items'])) {
            return;
        }

        foreach ($json_result_arr['response']['items'] as $item_arr) {
            $region_id = \Skif\Regions\RegionUtils::getRegionIdByVkId($item_arr['id']);

            if ($region_id) {
                continue;
            }

            $region_obj = new \Skif\Regions\Region();
            $region_obj->setVkId($item_arr['id']);
            $region_obj->setTitle($item_arr['title']);
            $region_obj->save();
        }

        echo 'Импорт регионов завершен';
    }
}