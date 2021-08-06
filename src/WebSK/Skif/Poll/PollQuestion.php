<?php

namespace WebSK\Skif\Poll;

use WebSK\Entity\Entity;
use WebSK\Entity\InterfaceWeight;
use WebSK\Entity\WeightTrait;

/**
 * Class PollQuestion
 * @package WebSK\Skif\Poll
 */
class PollQuestion extends Entity implements InterfaceWeight
{
    use WeightTrait;

    const DB_TABLE_NAME = 'poll_question';

    const _TITLE = 'title';
    protected string $title = '';

    const _POLL_ID = 'poll_id';
    protected int $poll_id;

    const _VOTES = 'votes';
    protected int $votes = 0;

    const _WEIGHT = 'weight';

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
}
