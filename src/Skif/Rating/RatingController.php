<?php

namespace Skif\Rating;

class RatingController extends \Skif\CRUD\CRUDController
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
     * @return float|mixed
     */
    public static function rateAction($rating_id)
    {
        $rating_star = isset($_REQUEST['rating_star']) ? floatval($_REQUEST['rating_star']) : '';

        $rating_obj = \Skif\Rating\Rating::factory($rating_id);

        $current_rating = $rating_obj->getRating();

        if (isset($_COOKIE[self::$rating_cookie_prefix . $rating_id])) {
            return $current_rating;
        }

        if (isset($_SESSION[self::$rating_cookie_prefix . $rating_id])) {
            return $current_rating;
        }

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (!$current_user_id) {
            return $current_rating;
        }

        $new_rating = ($current_rating + $rating_star) / 2;

        $rating_obj->setRating($new_rating);
        $rating_obj->save();

        setcookie(self::$rating_cookie_prefix . $rating_id, 'yes', time() + 3600 * 24); // Сутки
        $_SESSION[self::$rating_cookie_prefix . $rating_id] = 'yes';

        return $new_rating;
    }


}