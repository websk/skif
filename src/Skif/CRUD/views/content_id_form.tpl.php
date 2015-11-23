<br>
<div class="input-group">Введите url или id материала:</div>
<div class="input-group">
	<input type="text" class="form-control" value="" id="node-val">
	<span class="input-group-btn">
		<button class="btn btn-default" type="submit" id="node-save">Cохранить</button>
	</span>
</div>
<script>
	$("#node-save").on("click", function (e) {
		e.preventDefault();
		$.post("/crud/widget/nodeUrlParse", "node-url=" + $("#node-val").val(), function (data) {
			$("#node-id").val(data.node_id);
			$("#node-title").val(data.node_title + " (" + data.node_id + ")");
			$("#nid-ajax").removeClass("open").find(".glyphicon").removeClass("glyphicon-collapse-up").addClass("glyphicon-collapse-down");
	        $("#add-node").html("");
            $("#node-goto-link").attr("href", "/crud/edit/%5CSkif%5CNode%5CNode/" + data.node_id);
		}, "json")
		.fail(function() {
			alert("Неверный url или id материала");
		});
	});
</script>