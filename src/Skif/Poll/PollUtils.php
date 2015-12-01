<?php

namespace Skif\Poll;


class PollUtils
{

    public static function getDefaultPollId()
    {
        $poll_id = \Skif\DB\DBWrapper::readField(
            "SELECT id FROM poll WHERE is_default=1 AND is_published=1 LIMIT 1"
        );

        if (!$poll_id) {
            "SELECT id FROM poll WHERE is_published=1 ORDER BY id DESC LIMIT 1";
        }

        return $poll_id;
    }

    public static function getSumVotesFromPollQuestionByPoll($poll_id)
    {
        $poll_obj = \Skif\Poll\Poll::factory($poll_id);

        $poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

        $sum = 0;

        foreach ($poll_question_ids_arr as $poll_question_id) {
            $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);

            $sum += $poll_question_obj->getVotes();
        }

        return $sum;
    }

    public static function renderBlockByPollId($poll_id = null)
    {
        if (!$poll_id) {
            $poll_id = \Skif\Poll\PollUtils::getDefaultPollId();
        }

        if (!$poll_id) {
            return '';
        }

        return \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Poll',
            'block.tpl.php',
            array('poll_id' => $poll_id)
        );
    }

}