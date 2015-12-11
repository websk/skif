<?php

/**
 * @variable $model_class_name
 */

?>

<style>
    li a.glyphicon {
        color: #aaa;
    }

    li a.glyphicon:hover {
        color: #2a6496;
    }

</style>

<h2>
    <a id="add-link" style="font-size: 75%;" class="glyphicon glyphicon-plus" href="#" target="_blank"></a>
</h2>

<div class="model_editor_form">
<ul id="menu-tree" style="list-style-type: none;">
    <?= \Skif\Tree\Controller::renderTreeForClassName($model_class_name); ?>
</ul>
    </div>

<form id="setMenuWeightForm" action="/admin2/tree/set_weight" method="post">
    <input type="hidden" name="model_class_name" value="<?= $model_class_name ?>"/>
    <input type="hidden" name="target_menu_id" value=""/>
    <input type="hidden" name="menu_id" value=""/>
    <input type="hidden" name="direction" value=""/>
</form>
<form id="setMenuParentForm" action="/admin2/tree/set_parent" method="post">
    <input type="hidden" name="model_class_name" value="<?= $model_class_name ?>"/>
    <input type="hidden" name="parent_id" value=""/>
    <input type="hidden" name="menu_id" value=""/>
</form>
<form id="removeMenuForm" action="/admin2/tree/remove" method="post">
    <input type="hidden" name="model_class_name" value="<?= $model_class_name ?>"/>
    <input type="hidden" name="menu_id" value=""/>
</form>

<script>


    jQuery(function ($) {

        var selected_item = 0;

        var update_remove_links = function(){
            $("#menu-tree li").each(function(){
                $(this).parents("li").find(" > small .remove-menu-item").hide();
            });
        }

        var init_menu = function () {

            update_remove_links();

            $(".move-menu-item").unbind("click");
            $(".move-menu-item").click(function (e) {

                e.preventDefault();

                var this_li = $(this).closest("li");


                selected_item = this_li.data("menu-id");

                $("#menu-tree .glyphicon").hide();

                $(".change-parent-menu-item").show();

                $("#menu-tree .move-up-menu-item, #menu-tree .move-down-menu-item").show();
                this_li.find(".cancel-action").first().show();
                this_li.find(".actions .glyphicon").hide();

            });

            $(".move-up-menu-item").unbind("click");
            $('.move-up-menu-item').click(function (ev) {

                ev.preventDefault();

                if (!selected_item) {
                    return; // Exception?
                }

                var this_li = $(this).closest("li");
                var next_menu_id = this_li.data("menu-id")

                $('#setMenuWeightForm input[name=target_menu_id]').val(next_menu_id);
                $('#setMenuWeightForm input[name=menu_id]').val(selected_item);
                $('#setMenuWeightForm input[name=direction]').val("up");

                sendForm("setMenuWeightForm", function (data) {
                    var target_li = $("#menu-tree-" + selected_item);

                    this_li.before(target_li);

                    if (data.status != "success") {
                        alert("Произошла ошибка! Обновите страницу.");
                        return;
                    }

                    updateMenuItem(selected_item, data.menu_obj);
                    $(".cancel-action").first().click();
                });
            });

            $(".move-down-menu-item").unbind("click");
            $('.move-down-menu-item').click(function (ev) {

                ev.preventDefault();

                if (!selected_item) {
                    return; // Exception?
                }

                var this_li = $(this).closest("li");
                var prev_menu_id = this_li.data("menu-id")

                $('#setMenuWeightForm input[name=target_menu_id]').val(prev_menu_id);
                $('#setMenuWeightForm input[name=menu_id]').val(selected_item);
                $('#setMenuWeightForm input[name=direction]').val("down");

                sendForm("setMenuWeightForm", function (data) {

                    var target_li = $("#menu-tree-" + selected_item);

                    this_li.after(target_li);

                    if (data.status != "success") {
                        alert("Произошла ошибка! Обновите страницу.")
                        return;
                    }

                    updateMenuItem(selected_item, data.menu_obj);
                    $(".cancel-action").first().click();

                })
            });

            $(".change-parent-menu-item").unbind("click");
            $('.change-parent-menu-item').click(function (ev) {

                ev.preventDefault();

                if (!selected_item) {
                    return; // Exception?
                }

                var this_li = $(this).closest("li");
                var parent_menu_id = this_li.data("menu-id")

                $('#setMenuParentForm input[name=parent_id]').val(parent_menu_id);
                $('#setMenuParentForm input[name=menu_id]').val(selected_item);

                sendForm("setMenuParentForm", function (data) {

                    var target_li = $("#menu-tree-" + selected_item);

                    this_li.find("ul").first().prepend(target_li);

                    if (data.status != "success") {
                        alert("Произошла ошибка! Обновите страницу.")
                        return;
                    }

                    updateMenuItem(selected_item, data.menu_obj);
                    $(".cancel-action").first().click();

                })
            });

            $(".remove-menu-item").unbind("click");
            $('.remove-menu-item').click(function (ev) {

                ev.preventDefault();


                if (!confirm("Уверены?")) {
                    return;
                }

                var this_li = $(this).closest("li");
                var menu_id = this_li.data("menu-id");

                $('#removeMenuForm input[name=menu_id]').val(menu_id);

                sendForm("removeMenuForm", function (data) {

                    if (data.status != "success") {
                        alert("Произошла ошибка! Обновите страницу.")
                        return;
                    }

                    this_li.remove();

                    $(".cancel-action").first().click();
                })
            });

            $(".cancel-action").unbind("click");
            $(".cancel-action").click(function (e) {
                e.preventDefault();

                selected_item = 0;

                $("#menu-tree  .glyphicon").show();

                $("#menu-tree .actions  .glyphicon").hide();
                $(".move-menu-item").show();
                $(".cancel-action").hide();
                update_remove_links();
            });

        };

        $("#add-link").unbind("click");
        $("#add-link").click(function (e) {
            e.preventDefault();
            $.post("/admin2/tree/add_new", { model_class_name: <?= json_encode($model_class_name) ?> }, function (data) {

                if (data.status != "success") {
                    alert("Произошла ошибка! Обновите страницу.")
                    return;
                }

                if (data.menu_obj) {
                    var actions_html = $('.actions').html();

                    $("#menu-tree").prepend('\
                    <li id="menu-tree-' + data.menu_obj.id + '" data-parent-menu="0" data-menu-id="' + data.menu_obj.id + '" >\
                    <strong>\
                        <a href="/crud/edit/' + data.obj_class + '/' + data.menu_obj.id + '" class="menu-edit" target="_blank">' + data.menu_obj.text + '</a>\
                        </strong>\
                        <small>\
                            ' + data.additional_buttons + ' \
                            <a href="#" class="remove-menu-item glyphicon glyphicon-remove" title="Удалить"></a>\
                            <span class="actions">\
                            ' + actions_html + '\
                            </span>\
                        </small>\
                        <ul style=" list-style-type: none;">\
                        </ul>\
                    </li>');
                    init_menu();
                }

            }, "json");
        });

        function sendForm(form_id, callback) {
            var form = $("#" + form_id);
            var url = form.attr("action");
            var data = form.serialize();
            $.post(url, data, callback, "json");
        }

        function updateMenuItem(menu_id, menu_obj){

            var target_li = $("#menu-tree-" + menu_id);

            var new_parent = menu_obj.parent_id;
            target_li.data("parent-menu", new_parent);
            target_li.find(" > strong > .menu-edit").unwrap();
            if (new_parent == 0) {
                target_li.find(" > .menu-edit").wrap("<strong></strong>");
            }
        }


        init_menu();
    });
</script>
