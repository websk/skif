<?php
/**
 * @var $poll_id
 */

use Skif\Poll\Poll;
use Skif\Poll\PollQuestion;
use Skif\Poll\PollUtils;

$poll_obj = Poll::factory($poll_id);

$poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

$sum = PollUtils::getSumVotesFromPollQuestionByPoll($poll_id);
$max = PollUtils::getMaxVotesFromPollQuestionByPoll($poll_id);
?>
<div class="panel panel-default">
    <div class="panel-body">
<?php
foreach ($poll_question_ids_arr as $poll_question_id) {
    $poll_question_obj = PollQuestion::factory($poll_question_id);

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

