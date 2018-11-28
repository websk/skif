<?php

namespace WebSK\Skif\Rating;

use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Model\ActiveRecord;

class RatingVoice implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'rating_voice';

    /** @var int */
    protected $id;
    /** @var int */
    protected $rating_id;
    /** @var int */
    protected $rating = 0;
    /** @var string */
    protected $comment;
    /** @var int */
    protected $user_id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRatingId()
    {
        return $this->rating_id;
    }

    /**
     * @param int $rating_id
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
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public static function afterUpdate($id)
    {
        $rating_voice_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        Rating::afterUpdate($rating_voice_obj->getRatingId());
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        Rating::afterUpdate($this->getRatingId());
    }
}
