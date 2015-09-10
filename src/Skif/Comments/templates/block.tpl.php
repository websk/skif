<?php
/**
 * @var $url
 */
?>
<script type="text/javascript">
    $().ready(function() {
        $.ajax(
        {
            url:"/comments/list",
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
