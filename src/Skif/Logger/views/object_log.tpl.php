<?php
/**
 * @var array $logger_objs_arr
 */

use Skif\Users\User;
?>
<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<th>Дата и время</th>
            <th>IP адрес</th>
			<th>Пользователь</th>
			<th>Действие</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php

foreach ($logger_objs_arr as $logger_objs)
{
    $user_obj = User::factory($logger_objs->user_id, false);

	$username = $user_obj ? $user_obj->getName() : '';
    $record_url = '/admin/logger/record/' . urlencode($logger_objs->id);

?>
		<tr>
			<td><?= $logger_objs->ts ?></td>
            <td><?= $logger_objs->ip ?></td>
			<td><?= $username ?></td>
			<td><?= $logger_objs->action ?></td>
			<td align="center"><a href="<?=$record_url?>" class="glyphicon glyphicon-chevron-right"></a></td>
		</tr>
<?php
}
?>
	</tbody>
</table>