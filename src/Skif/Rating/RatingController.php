<?php

namespace Skif\Rating;

use WebSK\Skif\CRUD\CRUDController;
use WebSK\Skif\Auth\Auth;

class RatingController extends CRUDController
{

    protected static $model_class_name = '\Skif\Rating\Rating';
    public static $rating_cookie_prefix = 'rating_star_';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/rating';
    }

    public static function getRateUrl($rating_id)
    {
        return '/rating/' . $rating_id . '/rate';
    }

    /**
     * Оценка
     * @param $rating_id
     */
    public static function rateAction($rating_id)
    {
        $rating_star = isset($_REQUEST['rating_star']) ? floatval($_REQUEST['rating_star']) : 0;

        $rating_obj = Rating::factory($rating_id);

        $current_rating = $rating_obj->getRating();

        /*
        if (isset($_COOKIE[self::$rating_cookie_prefix . $rating_id])) {
            echo $current_rating;
            return;
        }

        if (isset($_SESSION[self::$rating_cookie_prefix . $rating_id])) {
            echo $current_rating;
            return;
        }
        */

        $current_user_id = Auth::getCurrentUserId();

        if (!$current_user_id) {
            echo $current_rating;
            return;
        }


        $rating_voice_id = RatingUtils::getRatingVoiceIdByRatingIdAndUserId($rating_id, $current_user_id);

        if ($rating_voice_id) {
            $rating_voice_obj = RatingVoice::factory($rating_voice_id);
        } else {
            $rating_voice_obj = new RatingVoice();

            $rating_voice_obj->setRatingId($rating_id);
            $rating_voice_obj->setUserId($current_user_id);
        }

        $rating_voice_obj->setRating($rating_star);
        $rating_voice_obj->save();

        $new_rating = RatingUtils::getRatingAverageByRatingId($rating_id);

        $rating_obj->setRating($new_rating);
        $rating_obj->save();

        /*
        setcookie(self::$rating_cookie_prefix . $rating_id, 'yes', time() + 3600 * 24); // Сутки
        $_SESSION[self::$rating_cookie_prefix . $rating_id] = 'yes';
        */

        echo $new_rating;
    }
}
