<?php
/**
 * @var $url
 */

use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Slim\Router;

?>
<script type="text/javascript">
    $().ready(function() {
        $.ajax(
        {
            url: "<?php echo Router::pathFor(CommentRoutes::ROUTE_NAME_COMMENTS_LIST); ?>",
            data: {
                'url': '<?php echo $url ?>'
            },
            type: "GET",
            dataType:"html",
            success: function(data, textStatus){
                $("#comments").html(data);
            }
        });
    });
</script>
<div id="comments"></div>
