<?php
/**
 * @var $poll_id
 */

use WebSK\Skif\Poll\PollController;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Slim\Container;

$poll_service = PollServiceProvider::getPollService(Container::self());
$poll_obj = $poll_service->getById($poll_id);

$poll_question_service = PollServiceProvider::getPollQuestionService(Container::self());
?>

<div><?php echo $poll_obj->getTitle(); ?></div>

<form action="<?php echo PollController::getVoteUrl($poll_id); ?>" method="post">

    <?php
    $poll_question_ids_arr = $poll_question_service->getIdsArrByPollId($poll_id);

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
                <a href="<?php echo PollController::getUrl($poll_id); ?>">Результаты</a>
            </div>
        </div>
    </div>
</form>
