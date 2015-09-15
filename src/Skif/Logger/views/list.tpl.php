<ul class="list-unstyled">
<?php
	foreach ($logger_objs_arr as $logger_objs)
	{
?>
	<li><a href="/admin/logger/object_log/<?= urlencode($logger_objs->entity_id) ?>"><?= $logger_objs->entity_id ?></a></li>
<?php
	}
?>
</ul>