<?php

namespace Skif\Poll;

use Websk\Skif\DBWrapper;
use Skif\PhpTemplate;

class PollUtils
{
    /**
     * @return int
     */
    public static function getDefaultPollId()
    {
        $poll_id = DBWrapper::readField(
            "SELECT id FROM " . Poll::DB_TABLE_NAME . " WHERE is_default=1 AND is_published=1 LIMIT 1"
        );

        if (!$poll_id) {
            $poll_id = DBWrapper::readField(
                "SELECT id FROM " . Poll::DB_TABLE_NAME . " WHERE is_published=1 ORDER BY id DESC LIMIT 1"
            );
        }

        return $poll_id;
    }

    /**
     * @param int $poll_id
     * @return int
     */
    public static function getSumVotesFromPollQuestionByPoll($poll_id)
    {
        $poll_obj = Poll::factory($poll_id);

        $poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

        $sum = 0;

        foreach ($poll_question_ids_arr as $poll_question_id) {
            $poll_question_obj = PollQuestion::factory($poll_question_id);

            $sum += $poll_question_obj->getVotes();
        }

        return $sum;
    }

    /**
     * @param int $poll_id
     * @return int
     */
    public static function getMaxVotesFromPollQuestionByPoll($poll_id)
    {
        $poll_obj = Poll::factory($poll_id);

        $poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

        $max = 0;
        $votes_arr = array();

        foreach ($poll_question_ids_arr as $poll_question_id) {
            $poll_question_obj = PollQuestion::factory($poll_question_id);

            if (in_array($poll_question_obj->getVotes(), $votes_arr)) {
                return 0;
            }

            if ($poll_question_obj->getVotes() > $max) {
                $max = $poll_question_obj->getVotes();
                $votes_arr[] = $max;
            }
        }

        return $max;
    }

    /**
     * @param int|null $poll_id
     * @return string
     */
    public static function renderBlockByPollId($poll_id = null)
    {
        if (!$poll_id) {
            $poll_id = self::getDefaultPollId();
        }

        if (!$poll_id) {
            return '';
        }

        return PhpTemplate::renderTemplateBySkifModule(
            'Poll',
            'block.tpl.php',
            array('poll_id' => $poll_id)
        );
    }
}
