<?php

namespace Skif\Rating;


class RatingUtils
{
    public static function getRatingIdByName($rating_name)
    {
        $rating_id = \Skif\DB\DBWrapper::readField(
            "SELECT id FROM " . \Skif\Rating\Rating::DB_TABLE_NAME . " WHERE name=? LIMIT 1",
            array($rating_name)
        );

        if (!$rating_id) {
            $rating_obj = new \Skif\Rating\Rating();
            $rating_obj->setName($rating_name);
            $rating_obj->save();

            $rating_id = $rating_obj->getId();
        }

        return $rating_id;
    }
}