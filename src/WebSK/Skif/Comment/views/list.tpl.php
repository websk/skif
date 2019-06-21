<?php
/**
 * @var $comments_ids_arr
 * @var $url
 */

use WebSK\Auth\Auth;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

$container = Container::self();

$comment_service = CommentServiceProvider::getCommentService($container);

$current_user_id = Auth::getCurrentUserId();
$current_user_is_admin = Auth::currentUserIsAdmin();
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

$crud = CRUDServiceProvider::getCrud($container);

foreach ($comments_ids_arr as $comment_id) {
    $comment_obj = $comment_service->getById($comment_id);
    ?>
    <div class="panel panel-default comment">
        <div class="panel-heading">
            <?php echo nl2br($comment_obj->getComment()); ?>
            <?php
            if (Auth::currentUserIsAdmin()) {
                echo '<a href="' . Router::pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT, ['comment_id' => $comment_id], ['destination' => $url . '#comments']) . '" class="btn btn-default btn-sm"><span class="fa fa-edit fa-lg text-warning fa-fw"></span></a>';
                echo (new CRUDTableWidgetDelete('', 'btn btn-default btn-sm', $url . '#comments'))->html($comment_obj, $crud);
            }
            ?>
            <div class="text-muted"><small><?= $comment_obj->getUserName() ?>, <?= date('d.m.Y', $comment_obj->getCreatedAtTs()) ?>
                <?php
                if ($current_user_is_admin && $comment_obj->getUserEmail()) {
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
            if ($current_user_is_admin && $children_comment_obj->getUserEmail()) {
                echo '<a href="' . Router::pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT, ['comment_id' => $children_comment_id], ['destination' => $url . '#comments']) . '" class="btn btn-default btn-sm"><span class="btn btn-default btn-sm"></span></a>';
                echo (new CRUDTableWidgetDelete('', 'btn btn-default btn-sm', $url . '#comments'))->html($children_comment_obj, $crud);
            }
            echo '</div>';
        }

        if ($current_user_is_admin || ($current_user_id && ($comment_obj->getUserId() == $current_user_id))) {
            ?>
            <p class="text-right" style="margin: 5px"><a href="#comment<?= $comment_obj->getId() ?>" class="btn btn-default btn-sm add_answer">Ответить</a></p>
        <?php
        }
        ?>
    </div>
<?php
}
