<?php
/**
 * @var list<int> $comments_ids_arr
 * @var string $url
 * @var null|User $current_user_obj
 * @var bool $current_user_is_admin
 * @var CommentService $comment_service
 * @var CRUD $crud_service
 */

use WebSK\Auth\User\User;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\Skif\Comment\CommentService;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentEditHandler;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentListHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentCreateHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentListHandler;
use WebSK\Skif\Router;

?>
    <script type="text/javascript">
        <?php
        if ($current_user_obj) {
        ?>
        $().ready(function () {
            $('.add_answer').on('click', function () {
                $(this).before('<form method="post" action="<?php echo Router::urlFor(CommentCreateHandler::class); ?>" id="comment_form_answer">'
                    + '<div class="mb-3"><textarea name="comment" class="form-control"></textarea></div>'
                    + '<input type="hidden" name="url" value="<?php echo $url ?>">'
                    + '<input type="hidden" name="parent_id" value="' + $(this).attr('href').substring(8) + '">'
                    + '<div class="mb-3"><input type="submit" value="Отправить сообщение" class="btn btn-outline-secondary btn-sm"></div>'
                    + '</form>'
                );
                $(this).remove();
            });
        });
        <?php
        }
        ?>
        $('#comment_pager li').on('click', function () {
            $.ajax(
                {
                    url: "<?php echo Router::urlFor(CommentListHandler::class); ?>",
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
    <article class="card card-secondary card-outline comment mb-3">
        <div class="card-body">
            <?php echo nl2br($comment_obj->getComment()); ?>

            <?php
            if ($current_user_is_admin) {
                echo '<div class="float-end">';

                echo '<a href="'
                    . Router::urlFor(AdminCommentEditHandler::class, ['comment_id' => $comment_id], ['destination' => $url . '#comments'])
                    . '" class="btn btn-outline-secondary btn-sm"><span class="fa fa-edit fa-lg text-warning fa-fw"></span></a>';

                echo (
                        new CRUDTableWidgetDelete(
                            '',
                            'btn btn-outline-secondary btn-sm',
                            $url . '#comments',
                            Router::urlFor(AdminCommentListHandler::class)
                        )
                )->html($comment_obj, $crud_service);

                echo '</div>';
            }
            ?>

            <div class="text-body-secondary mt-2">
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
            echo '<div class="card-footer bg-body-tertiary">';
        }

        foreach ($children_comments_ids_arr as $children_comment_id) {
            $children_comment_obj = $comment_service->getById($children_comment_id);

            echo nl2br($children_comment_obj->getComment());

            if (!$current_user_is_admin) {
                continue;
            }

            echo '<div class="float-end">';

            echo '<a href="'
                . Router::urlFor(AdminCommentEditHandler::class, ['comment_id' => $children_comment_id], ['destination' => $url . '#comments'])
                . '" class="btn btn-outline-secondary btn-sm"><span class="fa fa-edit fa-lg text-warning fa-fw"></span></a>';

            echo (
                    new CRUDTableWidgetDelete(
                        '',
                        'btn btn-outline-secondary btn-sm',
                        $url . '#comments',
                        Router::urlFor(AdminCommentListHandler::class)
                    )
            )->html($children_comment_obj, $crud_service);

            echo '</div>';
        }

        if ($children_comments_ids_arr) {
            echo '</div>';
        }

        if ($current_user_is_admin || ($current_user_obj && ($comment_obj->getUserId() == $current_user_obj->getId()))) {
            ?>
            <div class="card-footer bg-transparent">
                <a href="#comment<?php echo $comment_obj->getId(); ?>" class="btn btn-outline-secondary btn-sm add_answer">Ответить</a>
            </div>
            <?php
        }
        ?>
    </article>
    <?php
}
