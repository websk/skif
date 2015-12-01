<?php
/**
 * @var $poll_id
 */

$poll_obj = \Skif\Poll\Poll::factory($poll_id);

$poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

$sum = \Skif\Poll\PollUtils::getSumVotesFromPollQuestionByPoll($poll_id);
?>
<div class="row">
    <?php
    foreach ($poll_question_ids_arr as $poll_question_id) {
        $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);

        $vote_percentage = round($poll_question_obj->getVotes() / $sum * 100);
        ?>
        <div class="col-md-6"><?php echo $poll_question_obj->getTitle(); ?></div>
        <div class="col-md-6">
            <div class="progress">
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $vote_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $vote_percentage; ?>%">
                    <?php echo $vote_percentage; ?>%
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<p class="alert alert-info">Всего проголосовало: <?php echo $sum; ?> человек.</p>

