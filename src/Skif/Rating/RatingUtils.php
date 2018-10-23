<?php

namespace Skif\Rating;

use Websk\Skif\DBWrapper;

class RatingUtils
{
    /**
     * @param $rating_name
     * @return int
     */
    public static function getRatingIdByName($rating_name)
    {
        $rating_id = DBWrapper::readField(
            "SELECT id FROM " . Rating::DB_TABLE_NAME . " WHERE name=? LIMIT 1",
            [$rating_name]
        );

        if (!$rating_id) {
            $rating_obj = new Rating();
            $rating_obj->setName($rating_name);
            $rating_obj->save();

            $rating_id = $rating_obj->getId();
        }

        return (int)$rating_id;
    }

    /**
     * Расчет рейтинга
     * @param $rating_id
     * @return float|int
     */
    public static function getRatingAverageByRatingId($rating_id)
    {
        $rating_obj = Rating::factory($rating_id);

        $rating_voice_ids_arr = $rating_obj->getRatingVoicesIdsArr();

        if (!$rating_voice_ids_arr) {
            return 0;
        }

        $sum_rating = 0;

        foreach ($rating_voice_ids_arr as $rating_voice_id) {
            $rating_voice_obj = RatingVoice::factory($rating_voice_id);

            $sum_rating += $rating_voice_obj->getRating();
        }

        $average_rating = $sum_rating / count($rating_voice_ids_arr);

        return $average_rating;
    }

    /**
     * @param $rating_id
     * @param $user_id
     * @return int
     */
    public static function getRatingVoiceIdByRatingIdAndUserId($rating_id, $user_id)
    {
        $rating_voice_id = DBWrapper::readField(
            "SELECT id FROM " . RatingVoice::DB_TABLE_NAME . " WHERE rating_id=? AND user_id=? LIMIT 1",
            [$rating_id, $user_id]
        );

        return (int)$rating_voice_id;
    }
}
