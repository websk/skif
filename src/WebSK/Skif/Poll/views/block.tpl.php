<?php
/**
 * @var Poll $poll_obj
 * @var PollService $poll_service
 * @var PollQuestionService $poll_question_service
 */

use WebSK\Skif\Poll\Poll;
use WebSK\Skif\Poll\PollQuestionService;
use WebSK\Skif\Poll\PollService;
use WebSK\Skif\Poll\RequestHandlers\PollViewHandler;
use WebSK\Skif\Poll\RequestHandlers\PollVoteHandler;
use WebSK\Skif\Router;
use WebSK\Utils\Sanitize;
?>

<h2 class="h6 mb-3"><?php echo Sanitize::sanitizeTagContent($poll_obj->getTitle()); ?></h2>

<form action="<?php echo Router::urlFor(PollVoteHandler::class, ['poll_id' => $poll_obj->getId()]); ?>" method="post">

    <?php
    $poll_question_ids_arr = $poll_question_service->getIdsArrByPollId($poll_obj->getId());

    foreach ($poll_question_ids_arr as $poll_question_id) {
        $poll_question_obj = $poll_question_service->getById($poll_question_id);
        ?>
        <?php $poll_input_id = 'poll-question-' . $poll_question_id; ?>
        <div class="form-check mb-2">
            <input id="<?php echo $poll_input_id; ?>" class="form-check-input" type="radio" name="poll_question_id"
                   value="<?php echo $poll_question_id; ?>" required>
            <label class="form-check-label" for="<?php echo $poll_input_id; ?>"><?php echo Sanitize::sanitizeTagContent($poll_question_obj->getTitle()); ?></label>
        </div>
        <?php
    }
    ?>

    <div class="mt-3">
        <div class="row">
            <div class="col-md-6">
                <input type="submit" value="Голосовать" class="btn btn-primary btn-sm">
            </div>
            <div class="col-md-6 text-md-end">
                <a href="<?php echo Router::urlFor(PollViewHandler::class, ['poll_id' => $poll_obj->getId()]); ?>">Результаты</a>
            </div>
        </div>
    </div>
</form>
