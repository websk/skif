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
use WebSK\Slim\Router;
?>

<div><?php echo $poll_obj->getTitle(); ?></div>

<form action="<?php echo Router::pathFor(PollVoteHandler::class, ['poll_id' => $poll_obj->getId()]); ?>" method="post">

    <?php
    $poll_question_ids_arr = $poll_question_service->getIdsArrByPollId($poll_obj->getId());

    foreach ($poll_question_ids_arr as $poll_question_id) {
        $poll_question_obj = $poll_question_service->getById($poll_question_id);
        ?>
        <div class="radio">
            <label>
                <input type="radio" name="poll_question_id"
                       value="<?php echo $poll_question_id; ?>"><?php echo $poll_question_obj->getTitle(); ?>
            </label>
        </div>
        <?php
    }
    ?>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <input type="submit" value="Голосовать" class="btn btn-default btn-sm">
            </div>
            <div class="col-md-6 text-right">
                <a href="<?php echo Router::pathFor(PollViewHandler::class, ['poll_id' => $poll_obj->getId()]); ?>">Результаты</a>
            </div>
        </div>
    </div>
</form>
