<?php
/**
 * @var $field_value
 * @var $field_name
 */
$node_title = '';
$node_obj = null;
if ($field_value != '') {
    $media_obj = \Skif\Vdl\Media::factory($field_value, false);
    if($media_obj) {
        $node_obj = \Skif\Node\NodeFactory::loadNode($media_obj->getNid());
        if ($node_obj) {
            $node_title = $node_obj->getTitle() . ' (' . $field_value . ')';
        }
    }


}
?>
<div class="input-group">
    <?php if ($node_obj) { ?>
    <p class="form-control-static" id="nid-title">
        <a href="/vdl/media/<?= $field_value?>"><?= $node_title ?></a>
    </p>
    <?php } ?>
    <input type="hidden" id="node-id" name="<?= $field_name ?>" value="<?= $field_value ?>" />
</div>