<?php
/**
 * @var $search_value
 */

$page_search_form = \Skif\Util\CHtml::beginForm('/admin/blocks/search', 'post',
    array('role' => 'form', 'class' => 'form-inline', 'style' => 'display: inline-block; margin: 5px 20px')
);
$page_search_form .= \Skif\Util\TB::formGroup(
    \Skif\Util\Chtml::label('Поиск', false, array("class" => "sr-only")) .
    \Skif\Util\CHtml::textField('search', $search_value, array("class" => "form-control"))
);

$page_search_form .= '
    <button type="submit" class="btn btn-default">
        <span class="glyphicon glyphicon-search"></span>
    </button>';

$page_search_form .= \Skif\Util\CHtml::endForm();
?>
<div class="pull-right" style="margin-top: 22px;">
<?= $page_search_form ?>
</div>
