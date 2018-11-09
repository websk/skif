<?php
/**
 * @var $comments_ids_arr
 * @var $url
 */

$current_user_id = \WebSK\Skif\Auth\AuthUtils::getCurrentUserId();
?>
    <script type="text/javascript">
        <?php
        if ($current_user_id) {
        ?>
        $().ready(function () {
            $('.add_answer').bind('click', function () {
                $(this).before('<form method="post" action="/comments/add" id="comment_form_answer">'
                    + '<div class="form-group"><textarea name="comment" class="form-control"></textarea></div>'
                    + '<input type="hidden" name="url" value="<?php echo $url ?>">'
                    + '<input type="hidden" name="parent_id" value="' + $(this).attr('href').substring(8) + '">'
                    + '<div class="form-group"><input type="submit" value="Отправить сообщение" class="btn btn-default btn-sm"></div>'
                    + '</form>'
                );
                $(this).remove();
            });
        });
        <?php
        }
        ?>
        $('#comment_pager li').bind('click', function () {
            $.ajax(
                {
                    url: "/comments/list",
                    data: {
                        'url': '<?php echo $url ?>',
                        'page': $(this).text()
                    },
                    type: "GET",
                    dataType: "html",
                    success: function (data, textStatus) {
                        $("#comments").html(data);
                    }
                });
        });
    </script>
<?php

foreach ($comments_ids_arr as $comment_id) {
    $comment_obj = \Skif\Comment\Comment::factory($comment_id);
    ?>
    <div class="panel panel-default comment">
        <div class="panel-heading">
            <?php echo nl2br($comment_obj->getComment()); ?>
            <?php
            if (\WebSK\Skif\Auth\AuthUtils::currentUserIsAdmin()) {
                echo ' [&nbsp;<a href="' . \Skif\Comment\CommentController::getEditUrl(\Skif\Comment\CommentController::getModelClassName(), $comment_id) . '?destination=' . $url . '#comments">Изменить</a>&nbsp;]';
                echo ' [&nbsp;<a href="' . \Skif\Comment\CommentController::getDeleteUrl(\Skif\Comment\CommentController::getModelClassName(), $comment_id) . '?destination=' . $url . '#comments" onClick="return confirm(\'Вы уверены, что хотите удалить?\')">Удалить</a>&nbsp;]';
            }
            ?>
            <div class="text-muted"><small><?= $comment_obj->getUserName() ?>, <?= date('d.m.Y', $comment_obj->getUnixTime()) ?>
                <?php
                if (\WebSK\Skif\Auth\AuthUtils::currentUserIsAdmin() && $comment_obj->getUserEmail()) {
                    echo ', ' . $comment_obj->getUserEmail();
                }
                ?>
                </small>
            </div>
        </div>
        <?php
        $children_comments_ids_arr = $comment_obj->getChildrenIdsArr();
        foreach ($children_comments_ids_arr as $children_comment_id) {
            $children_comment_obj = \Skif\Comment\Comment::factory($children_comment_id);

            echo '<div class="panel-body">' . nl2br($children_comment_obj->getComment());
            if (\WebSK\Skif\Auth\AuthUtils::currentUserIsAdmin()) {
                echo ' [&nbsp;<a href="' . \Skif\Comment\CommentController::getEditUrl(\Skif\Comment\CommentController::getModelClassName(), $children_comment_id) . '?destination=' . $url . '#comments">Изменить</a>&nbsp;]';
                echo ' [&nbsp;<a href="' . \Skif\Comment\CommentController::getDeleteUrl(\Skif\Comment\CommentController::getModelClassName(), $children_comment_id) . '?destination=' . $url . '#comments" onClick="return confirm(\'Вы уверены, что хотите удалить?\')">Удалить</a>&nbsp;]';
            }
            echo '</div>';
        }

        if ($current_user_id) {
            ?>
            <p class="text-right"><a href="#comment<?= $comment_obj->getId() ?>" class="add_answer">Ответить</a></p>
        <?php
        }
        ?>
    </div>
<?php
}
