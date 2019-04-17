<?php

namespace WebSK\Skif\Rating;

use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Model\ActiveRecord;

/**
 * Class Rating
 * @package WebSK\Skif\Rating
 */
class Rating implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'rating';

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var int */
    protected $rating = 0;

    protected $rating_voices_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'rating_voices_ids_arr',
    );

    public static $crud_model_class_screen_name_for_list = 'Рейтинги';

    // Связанные модели
    public static $related_models_arr = array(
        RatingVoice::class => array(
            'link_field' => 'rating_id',
            'field_name' => 'rating_voices_ids_arr',
            'list_title' => 'Оценки пользователей',
        ),
    );

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return array
     */
    public function getRatingVoicesIdsArr()
    {
        return $this->rating_voices_ids_arr;
    }
}
