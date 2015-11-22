<?php
/**
 * @var $redirect_objs_arr
 */
?>
<p class="padding_top_10 padding_bottom_10">
	<a href="/admin/redirect/add" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
</p>

<table class="table table-striped table-hover">
	<colgroup>
		<col class="col-md-1 col-sm-1 col-xs-1">
		<col class="col-md-7 col-sm-6 col-xs-6">
		<col class="col-md-1 hidden-sm hidden-xs">
		<col class="col-md-3 col-sm-5 col-xs-5">
	</colgroup>
	<tbody>
	<?php
		foreach ($redirect_objs_arr as $value) {
	?>
		<tr>
			<td><?php echo $value->id; ?></td>
			<td>
				<div class="text-muted"><?= $value->src ?></div>
				<div class="text-primary"><strong><?= $value->dst ?></strong></div>
			</td>
			<td  class="hidden-sm hidden-xs"><?= $value->code ?></td>
			<td align="right">
				<a href="/admin/redirect/edit/<?= $value->id ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
					<span class="fa fa-edit fa-lg text-warning fa-fw"></span>
				</a>
				<a href="/admin/redirect/delete?action=deleteredirect&redirect_id=<?php echo $value->id; ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
					<span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
				</a>
			</td>
		</tr>
	<?php
		}
	?>
	<tbody>
</table>
