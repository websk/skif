<?php

$page_search_form = \Skif\Util\CHtml::beginForm('/admin/blocks/search', 'post', array('role' => 'form', 'class' => 'form-inline', 'style' => 'display: inline-block; margin: 5px 20px'));
$page_search_form .= \Skif\Util\TB::formGroup(
    \Skif\Util\Chtml::label('Поиск', false, array("class" => "sr-only")) .
    \Skif\Util\CHtml::textField('search', "", array("class" => "form-control"))
);

$page_search_form .= '
    <button type="submit" class="btn btn-default" title="Поиск по тексту блоков">
        <span class="glyphicon glyphicon-search"></span>
    </button>';
$theme = \Skif\Blocks\ControllerBlocks::getEditorTheme();
$page_search_form .= \Skif\Util\CHtml::endForm();
?>

<div class="pull-right" style="margin-top: 22px;">
<?= $page_search_form ?>
<a title="Создать блок" class="btn btn-default" href="/admin/blocks/edit/NEW?theme=<?= $theme ?>"><span class="glyphicon glyphicon-plus"></span></a>
</div>

<script>
    function getCookie(name) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        } else {
            begin += 2;
            var end = document.cookie.indexOf(";", begin);
            if (end == -1) {
                end = dc.length;
            }
        }
        return decodeURI(dc.substring(begin + prefix.length, end));
    }

    function setDebugCookieStatusOnLoad(){
        var debugCookie = getCookie("spb_theme_debug");

        if (debugCookie == 1) {
            document.getElementById("debugCookie").className="btn btn-success";
        } else {
            document.getElementById("debugCookie").className="btn btn-default";
        }

        return false;
    }

    setDebugCookieStatusOnLoad();
    </script>
