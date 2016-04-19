<?php

namespace Skif\Rating;


class RatingVoice implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'rating_voice';

    protected $id;
    protected $rating_id;
    protected $rating = 0;
    protected $comment;

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
    public function getRatingId()
    {
        return $this->rating_id;
    }

    /**
     * @param mixed $rating_id
     */
    public function setRatingId($rating_id)
    {
        $this->rating_id = $rating_id;
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
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public static function afterUpdate($id)
    {
        $rating_voice_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        \Skif\Rating\Rating::afterUpdate($rating_voice_obj->getRatingId());
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Rating\Rating::afterUpdate($this->getRatingId());
    }

}