<script>
function deleteComfirm() {
	if (confirm("Вы действительно хотите удалить этот Redirect #<?= $redirect_objs_arr -> id ?>?")) {
		$('#DelForm').submit();
	}
}
</script>
<form id="DelForm" action="/admin/redirect/delete" method="post">
	<input type="hidden" value="deleteredirect" name="action">
	<input type="hidden" value="<?= $redirect_objs_arr -> id ?>" name="redirect_id">
</form>
<form role="form" action="/admin/redirect/save" method="post">
	<input type="hidden" value="saveredirect" name="action">
	<input type="hidden" value="<?= $redirect_objs_arr -> id ?>" name="id">
	<div class="col-md-6" style="padding-left:0;">		
		<div class="form-group">
			<label for="Kind">Kind</label>
			<select name="kind" id="Kind" class="form-control">
				<option value="1" <?= (($redirect_objs_arr -> kind == "1")?'selected':'') ?>>1 - строка</option>
				<option value="2" <?= (($redirect_objs_arr -> kind == "2")?'selected':'') ?>>2 - регексп</option>
			</select>
			<p class="help-block"><b>Вид:</b> (1 - строка, 2 - регексп)</p>
		</div>
	</div>
	<div class="col-md-6" style="padding-right:0;">
		<div class="form-group">
			<label for="HTTP">HTTP-код:</label>
			<input type="text" class="form-control" name="code" value="<?= $redirect_objs_arr -> code ?>" id="HTTP" placeholder="Введите HTTP-код">
			<p class="help-block">Номер http-кода</p>
		</div>
	</div>
	<div class="form-group">
		<label for="Src">Src</label>
		<input type="text" class="form-control" name="src" value="<?= $redirect_objs_arr -> src ?>" id="Src" placeholder="">
		<p class="help-block"><b>Строка:</b> /redirfrom &nbsp; <b>Регексп:</b> /part/</p>
	</div>
	<div class="form-group">
		<label for="Dst">Dst</label>
		<input type="text" class="form-control" name="dst" value="<?= $redirect_objs_arr -> dst ?>" id="Dst" placeholder="">
		<p class="help-block">redirto</p>
	</div>
	<button type="submit" class="btn btn-primary">Сохранить</button>
	<a href="/admin/redirect/list" class="btn btn-default">Отмена</a>
	<a href="#" class="btn btn-danger pull-right" onclick="deleteComfirm();return false;">Удалить</a>
</form>
