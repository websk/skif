<?php
/**
 * @var int $poll_id
 * @var PollQuestionService $poll_question_service
 */

use WebSK\Skif\Poll\PollQuestionService;

$poll_question_ids_arr = $poll_question_service->getIdsArrByPollId($poll_id);

$sum = $poll_question_service->getSumVotesFromPollQuestionByPoll($poll_id);
$max = $poll_question_service->getMaxVotesFromPollQuestionByPoll($poll_id);
?>
<div class="panel panel-default">
    <div class="panel-body">
<?php
foreach ($poll_question_ids_arr as $poll_question_id) {
    $poll_question_obj = $poll_question_service->getById($poll_question_id);

    $vote_percentage = $sum ? round($poll_question_obj->getVotes() / $sum * 100) : 0;
    ?>
    <div>
        <div><?php echo $poll_question_obj->getTitle(); ?></div>
        <div>
            <div class="progress">
                <div class="progress-bar<?php echo ($max && ($poll_question_obj->getVotes() == $max) ? ' progress-bar-success' : '') ?>" role="progressbar" aria-valuenow="<?php echo $vote_percentage; ?>"
                     aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $vote_percentage; ?>%">
                    <?php echo $vote_percentage; ?>%
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
    </div>
</div>
<p></p>


<p class="alert alert-info">Всего проголосовало: <?php echo $sum; ?> человек.</p>

