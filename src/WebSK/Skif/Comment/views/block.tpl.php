<?php
/**
 * @var string $url
 */

use WebSK\Skif\Comment\RequestHandlers\CommentListHandler;
use WebSK\Slim\Router;

?>
<script type="text/javascript">
    $().ready(function() {
        $.ajax(
        {
            url: "<?php echo Router::urlFor(CommentListHandler::class); ?>",
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
