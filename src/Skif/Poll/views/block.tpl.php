<?php
/**
 * @var $poll_id
 */

$poll_obj = \Skif\Poll\Poll::factory($poll_id);
?>

<div><?php echo $poll_obj->getTitle(); ?></div>

<form action="<?php echo \Skif\Poll\PollController::getVoteUrl($poll_id); ?>" method="post">

    <?php
    $poll_question_ids_arr = $poll_obj->getPollQuestionsIdsArr();

    foreach ($poll_question_ids_arr as $poll_question_id) {
        $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);
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
                <a href="<?php echo $poll_obj->getUrl(); ?>">Результаты</a>
            </div>
        </div>
    </div>
</form>
