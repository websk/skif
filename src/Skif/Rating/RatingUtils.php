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

    /**
     * Расчет рейтинга
     * @param $rating_id
     * @return float|int
     */
    public static function getRatingAverageByRatingId($rating_id)
    {
        $rating_obj = \Skif\Rating\Rating::factory($rating_id);

        $rating_voice_ids_arr = $rating_obj->getRatingVoicesIdsArr();

        if (!$rating_voice_ids_arr) {
            return 0;
        }

        $sum_rating = 0;

        foreach ($rating_voice_ids_arr as $rating_voice_id) {
            $rating_voice_obj = \Skif\Rating\RatingVoice::factory($rating_voice_id);

            $sum_rating += $rating_voice_obj->getRating();
        }

        $average_rating = $sum_rating / count($rating_voice_ids_arr);

        return $average_rating;
    }

    /**
     * @param $rating_id
     * @param $user_id
     * @return mixed
     */
    public static function getRatingVoiceIdByRatingIdAndUserId($rating_id, $user_id)
    {
        $rating_voice_id = \Skif\DB\DBWrapper::readField(
            "SELECT id FROM " . \Skif\Rating\RatingVoice::DB_TABLE_NAME . " WHERE rating_id=? AND user_id=? LIMIT 1",
            array($rating_id, $user_id)
        );

        return $rating_voice_id;
    }
}