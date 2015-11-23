<?php

$node_title = '';
$node_edit_url = \Skif\CRUD\ControllerCRUD::getEditUrl('\Skif\Node\Node', 0);
$node_title_link = '<a href="'. $node_edit_url .'">' . $node_title . '</a>';

if( $field_value != '' )
{
	$node_obj = \Skif\Node\NodeFactory::loadNode($field_value);
	if($node_obj) {
        $node_title = $node_obj->getTitle();
        $node_edit_url = \Skif\CRUD\ControllerCRUD::getEditUrl('\Skif\Node\Node', $field_value);
        $node_title_link = '<a href="'. $node_edit_url .'">' . $node_title . '</a>';
	}
}
?>
<div class="row">
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static" id="<?= $field_name ?>-title"><?= $node_title_link ?></p>
        <p class="form-control-static" id="<?= $field_name ?>-invalid" style="display:none; color:red;">Неверный url или id материала</p>
    </div>
    <div class="col-sm-3 col-xs-12">
        <input type="text" class="form-control" value="<?= $field_value ?>" id="<?= $field_name ?>-id" placeholder="введите ID" name="<?= $field_name ?>">
    </div>
</div>
<script>
(function() {
    var node_id = $('#<?= $field_name ?>-id');
    var node_title = $('#<?= $field_name ?>-title');
    var node_link = node_title.find('a');
    var invalid = $('#<?= $field_name ?>-invalid');

    var prev_node_id = node_id.val();

    function update() {
        if (prev_node_id != node_id.val()) {
            prev_node_id = node_id.val();

            $.post("/crud/widget/nodeUrlParse", "node-url=" + node_id.val(), function(data) {
                invalid.hide();
                node_title.show();

                node_id.val(data.node_id);
                prev_node_id = node_id.val();

                var edit_url = node_link.attr('href').replace(/(\d+)$/, data.node_id);
                node_link.attr('href', edit_url);

                node_link.text(data.node_title);
            }, 'json').fail(function() {
                node_title.hide();
                invalid.show();
            });
        }
    }

    node_id.on('keyup change', function(e) {
        e.preventDefault();
        update();
    });

    setInterval(update, 1000);
})();
</script>
