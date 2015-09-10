<a href="/admin/redirect/add" class="btn btn-primary">Добавить</a>
<hr>
<table class="table table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Src/Dst</th>
			<th>Code</th>
			<th>Kind</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach ($redirect_objs_arr as $value)
		{
	?>
		<tr>
			<td><?= $value->id ?></td>
			<td>
				<div class="text-muted"><?= $value->src ?></div>
				<div class="text-primary"><strong><?= $value->dst ?></strong></div>
			</td>
			<td align="center"><?= $value->code ?></td>
			<td align="center"><?= $value->kind ?></td>
			<td><a href="/admin/redirect/edit/<?= $value->id ?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a></td>
		</tr>
	<?php
		}
	?>
	<tbody>
</table>
<hr>
<a href="/admin/redirect/add" class="btn btn-primary">Добавить</a>