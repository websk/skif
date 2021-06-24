<?php
/**
 * @var array $comments_ids_arr
 * @var string $url
 * @var null|User $current_user_obj
 * @var bool $current_user_is_admin
 * @var CommentService $comment_service
 */

use WebSK\Auth\User\User;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentService;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentEditHandler;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentListHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentCreateHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentListHandler;
use WebSK\Slim\Container;
use WebSK\Slim\Router;

?>
    <script type="text/javascript">
        <?php
        if ($current_user_obj) {
        ?>
        $().ready(function () {
            $('.add_answer').bind('click', function () {
                $(this).before('<form method="post" action="<?php echo Router::pathFor(CommentCreateHandler::class); ?>" id="comment_form_answer">'
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
                    url: "<?php echo Router::pathFor(CommentListHandler::class); ?>",
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

$container = Container::self();
$crud = CRUDServiceProvider::getCrud($container);

foreach ($comments_ids_arr as $comment_id) {
    $comment_obj = $comment_service->getById($comment_id);
    ?>
    <div class="panel panel-default comment">
        <div class="panel-heading">
            <?php echo nl2br($comment_obj->getComment()); ?>

            <?php
            if ($current_user_is_admin) {
                echo '<div class="pull-right">';

                echo '<a href="'
                    . Router::pathFor(AdminCommentEditHandler::class, ['comment_id' => $comment_id], ['destination' => $url . '#comments'])
                    . '" class="btn btn-default btn-sm"><span class="fa fa-edit fa-lg text-warning fa-fw"></span></a>';

                echo (
                        new CRUDTableWidgetDelete(
                            '',
                            'btn btn-default btn-sm',
                            $url . '#comments',
                            Router::pathFor(AdminCommentListHandler::class)
                        )
                )->html($comment_obj, $crud);

                echo '</div>';
            }
            ?>

            <div class="text-muted">
                <small><?php echo $comment_service->getUserName($comment_obj) ?>, <?= date('d.m.Y', $comment_obj->getCreatedAtTs()) ?>
                    <?php
                    if ($current_user_is_admin && $comment_service->getUserEmail($comment_obj)) {
                        echo ', ' . $comment_service->getUserEmail($comment_obj);
                    }
                    ?>
                </small>
            </div>
        </div>
        <?php
        $children_comments_ids_arr = $comment_service->getChildrenIdsArr($comment_obj->getId());

        if ($children_comments_ids_arr) {
            echo '<div class="panel-body">';
        }

        foreach ($children_comments_ids_arr as $children_comment_id) {
            $children_comment_obj = $comment_service->getById($children_comment_id);

            echo nl2br($children_comment_obj->getComment());

            if (!$current_user_is_admin) {
                continue;
            }

            echo '<div class="pull-right">';

            echo '<a href="'
                . Router::pathFor(AdminCommentEditHandler::class, ['comment_id' => $children_comment_id], ['destination' => $url . '#comments'])
                . '" class="btn btn-default btn-sm"><span class="fa fa-edit fa-lg text-warning fa-fw"></span></a>';

            echo (
                    new CRUDTableWidgetDelete(
                        '',
                        'btn btn-default btn-sm',
                        $url . '#comments',
                        Router::pathFor(AdminCommentListHandler::class)
                    )
            )->html($children_comment_obj, $crud);

            echo '</div>';
        }

        if ($children_comments_ids_arr) {
            echo '</div>';
        }

        if ($current_user_is_admin || ($current_user_obj && ($comment_obj->getUserId() == $current_user_obj->getId()))) {
            ?>
            <div class="text-left" style="margin: 5px">
                <a href="#comment<?php echo $comment_obj->getId(); ?>" class="btn btn-default btn-sm add_answer">Ответить</a>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
