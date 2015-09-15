<form role="form" action="/admin/redirect/save" method="post">
	<input type="hidden" value="addredirect" name="action">
	<div class="col-md-6" style="padding-left:0;">		
		<div class="form-group">
			<label for="Kind">Kind</label>
			<select name="kind" id="Kind" class="form-control">
				<option value="1">1 - строка</option>
				<option value="2">2 - регексп</option>
			</select>
			<p class="help-block"><b>Вид:</b> (1 - строка, 2 - регексп)</p>
		</div>
	</div>
	<div class="col-md-6" style="padding-right:0;">
		<div class="form-group">
			<label for="HTTP">HTTP-код:</label>
			<input type="text" class="form-control" name="code" value="301" id="HTTP" placeholder="Введите HTTP-код">
			<p class="help-block">Номер http-кода</p>
		</div>
	</div>
	<div class="form-group">
		<label for="Src">Src</label>
		<input type="text" class="form-control" name="src" value="" id="Src" placeholder="">
		<p class="help-block"><b>Строка:</b> /redirfrom &nbsp; <b>Регексп:</b> /part/</p>
	</div>
	<div class="form-group">
		<label for="Dst">Dst</label>
		<input type="text" class="form-control" name="dst" value="" id="Dst" placeholder="">
		<p class="help-block">redirto</p>
	</div>
	<button type="submit" class="btn btn-primary">Добавить</button>
	<a href="/admin/redirect/list" class="btn btn-default">Отмена</a>
	<button type="reset" class="btn btn-danger pull-right">Очистить</button>
</form>