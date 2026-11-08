<?php
/**
 * @var int $poll_id
 * @var PollQuestionService $poll_question_service
 */

use WebSK\Skif\Poll\PollQuestionService;
use WebSK\Utils\Sanitize;

$poll_question_ids_arr = $poll_question_service->getIdsArrByPollId($poll_id);

$sum = $poll_question_service->getSumVotesFromPollQuestionByPoll($poll_id);
$max = $poll_question_service->getMaxVotesFromPollQuestionByPoll($poll_id);
?>
<div class="card card-info card-outline mb-3">
    <div class="card-body">
<?php
foreach ($poll_question_ids_arr as $poll_question_id) {
    $poll_question_obj = $poll_question_service->getById($poll_question_id);

    $vote_percentage = $sum ? round($poll_question_obj->getVotes() / $sum * 100) : 0;
    ?>
    <div class="mb-3">
        <div class="mb-1"><?php echo Sanitize::sanitizeTagContent($poll_question_obj->getTitle()); ?></div>
        <div>
            <div class="progress" role="group" aria-label="Результат: <?php echo Sanitize::sanitizeAttrValue($poll_question_obj->getTitle()); ?>">
                <div class="progress-bar<?php echo ($max && ($poll_question_obj->getVotes() == $max) ? ' bg-success' : '') ?>" role="progressbar" aria-valuenow="<?php echo $vote_percentage; ?>"
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
<div class="alert alert-info" role="status">Всего проголосовало: <?php echo $sum; ?> человек.</div>

