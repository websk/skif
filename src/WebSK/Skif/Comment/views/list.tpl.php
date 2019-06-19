<?php
/**
 * @var $comments_ids_arr
 * @var $url
 */

use WebSK\Auth\Auth;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

$comment_service = CommentServiceProvider::getCommentService(Container::self());

$current_user_id = Auth::getCurrentUserId();
?>
    <script type="text/javascript">
        <?php
        if ($current_user_id) {
        ?>
        $().ready(function () {
            $('.add_answer').bind('click', function () {
                $(this).before('<form method="post" action="<?php echo Router::pathFor(CommentRoutes::ROUTE_NAME_COMMENTS_CREATE); ?>" id="comment_form_answer">'
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
                    url: "<?php echo Router::pathFor(CommentRoutes::ROUTE_NAME_COMMENTS_LIST); ?>",
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
    $comment_obj = $comment_service->getById($comment_id);
    ?>
    <div class="panel panel-default comment">
        <div class="panel-heading">
            <?php echo nl2br($comment_obj->getComment()); ?>
            <?php
            if (Auth::currentUserIsAdmin()) {
                echo ' [&nbsp;<a href="' . Router::pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT, ['comment_id' => $comment_id]) . '?destination=' . $url . '#comments">Изменить</a>&nbsp;]';
                echo ' [&nbsp;<a href="' . CommentController::getDeleteUrl(CommentController::getModelClassName(), $comment_id) . '?destination=' . $url . '#comments" onClick="return confirm(\'Вы уверены, что хотите удалить?\')">Удалить</a>&nbsp;]';
            }
            ?>
            <div class="text-muted"><small><?= $comment_obj->getUserName() ?>, <?= date('d.m.Y', $comment_obj->getUnixTime()) ?>
                <?php
                if (Auth::currentUserIsAdmin() && $comment_obj->getUserEmail()) {
                    echo ', ' . $comment_obj->getUserEmail();
                }
                ?>
                </small>
            </div>
        </div>
        <?php
        $children_comments_ids_arr = $comment_service->getChildrenIdsArr($comment_obj->getId());
        foreach ($children_comments_ids_arr as $children_comment_id) {
            $children_comment_obj = $comment_service->getById($children_comment_id);

            echo '<div class="panel-body">' . nl2br($children_comment_obj->getComment());
            if (Auth::currentUserIsAdmin()) {
                echo ' [&nbsp;<a href="' . Router::pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT, ['comment_id' => $children_comment_id]) . '?destination=' . $url . '#comments">Изменить</a>&nbsp;]';
                echo ' [&nbsp;<a href="' . CommentController::getDeleteUrl(CommentController::getModelClassName(), $children_comment_id) . '?destination=' . $url . '#comments" onClick="return confirm(\'Вы уверены, что хотите удалить?\')">Удалить</a>&nbsp;]';
            }
            echo '</div>';
        }

        if (Auth::currentUserIsAdmin() || ($current_user_id && ($comment_obj->getUserId() == $current_user_id))) {
            ?>
            <p class="text-right" style="margin: 5px"><a href="#comment<?= $comment_obj->getId() ?>" class="btn btn-default btn-sm add_answer">Ответить</a></p>
        <?php
        }
        ?>
    </div>
<?php
}
