<?php
/**
 * @var $poll_id
 */

$poll_obj = \Skif\Poll\Poll::factory($poll_id);

$poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

$sum = \Skif\Poll\PollUtils::getSumVotesFromPollQuestionByPoll($poll_id);
?>

<table border="0">
    <?php
    foreach ($poll_question_ids_arr as $poll_question_id) {
        $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);

        $vote_percentage = round($poll_question_obj->getVotes() / $sum * 100);
        ?>
        <tr>
            <td width="200" align="right"><?php echo $poll_question_obj->getTitle(); ?></td>
            <td width="280">
                <img src="/assets/images/img.gif" width="<?php echo $vote_percentage; ?>" height="10" border="0">
                <?php echo $vote_percentage; ?>%
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<p>Всего проголосовало: <?php echo $sum; ?> человек.</p>

