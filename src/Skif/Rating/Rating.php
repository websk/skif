<?php

namespace Skif\Rating;

use Skif\Model\FactoryTrait;
use Skif\Model\InterfaceDelete;
use Skif\Model\InterfaceFactory;
use Skif\Model\InterfaceLoad;
use Skif\Model\InterfaceSave;
use Skif\Util\ActiveRecord;

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
