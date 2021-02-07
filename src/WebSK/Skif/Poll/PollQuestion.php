<?php

namespace WebSK\Skif\Poll;

use WebSK\Entity\Entity;

/**
 * Class PollQuestion
 * @package WebSK\Skif\Poll
 */
class PollQuestion extends Entity
{

    const ENTITY_SERVICE_CONTAINER_ID = 'skif.poll_question_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.poll_question_repository';
    const DB_TABLE_NAME = 'poll_question';

    const _TITLE = 'title';
    /** @var string */
    protected $title = '';

    const _POLL_ID = 'poll_id';
    /** @var int */
    protected $poll_id;

    const _VOTES = 'votes';
    /** @var int */
    protected $votes = 0;

    const _WEIGHT = 'weight';
    /** @var int */
    protected $weight = 0;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getPollId(): int
    {
        return $this->poll_id;
    }

    /**
     * @param int $poll_id
     */
    public function setPollId(int $poll_id): void
    {
        $this->poll_id = $poll_id;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

    /**
     * @param int $votes
     */
    public function setVotes(int $votes): void
    {
        $this->votes = $votes;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }
}
