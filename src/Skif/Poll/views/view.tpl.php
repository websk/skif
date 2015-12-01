<?php
/**
 * @var $poll_id
 */

$poll_obj = \Skif\Poll\Poll::factory($poll_id);

$poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

$sum = \Skif\Poll\PollUtils::getSumVotesFromPollQuestionByPoll($poll_id);
?>
<?php
foreach ($poll_question_ids_arr as $poll_question_id) {
    $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);

    $vote_percentage = round($poll_question_obj->getVotes() / $sum * 100);
    ?>
    <div>
        <div><?php echo $poll_question_obj->getTitle(); ?></div>
        <div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $vote_percentage; ?>"
                     aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $vote_percentage; ?>%">
                    <?php echo $vote_percentage; ?>%
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
<p class="alert alert-info">Всего проголосовало: <?php echo $sum; ?> человек.</p>

