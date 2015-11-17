<?php
/**
 * @var $search_value
 */

if (!isset($search_value)) {
    $search_value = '';
}
?>

<div class="jumbotron">
    <div class="row">
        <div class="col-md-6 col-xs-6">
            <form role="form" class="form-inline" action="/admin/blocks/search" method="post">
                <div class="form-group">
                    <label class="sr-only">Поиск</label>
                    <input class="form-control" type="text" value="<?php echo $search_value; ?>" name="search" id="search" placeholder="Поиск">
                </div>
                <button type="submit" class="btn btn-default" title="Поиск по тексту блоков">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </form>
        </div>
        <div class="col-md-6 col-xs-6">

        </div>
    </div>
</div>

<p class="padding_top_10 padding_bottom_10">
    <a href="/admin/blocks/edit/new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить блок</a>
</p>

