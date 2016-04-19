<?php

namespace Skif\Rating;

class Rating implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'rating';

    protected $id;
    protected $name;
    protected $rating = 0;

    protected $rating_voices_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'rating_voices_ids_arr',
    );

    // Связанные модели
    public static $related_models_arr = array(
        \Skif\Rating\RatingVoice::class => array(
            'link_field' => 'rating_id',
            'field_name' => 'rating_voices_ids_arr',
            'list_title' => 'Оценки пользователей',
        ),
    );

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getRatingVoicesIdsArr()
    {
        return $this->rating_voices_ids_arr;
    }
    

}