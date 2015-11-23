<?php
/**
 * @var $field_name
 * @var $field_value
 */

$game_title = '';
$default_date = date('Y-m-d', time());

if( $field_value != '' )
{
	$game_obj = \Skif\Stats\GameFactory::loadGame($field_value);
	if($game_obj) {
		$game_title = $game_obj->getHeadTitle() . ' (' . $field_value . ')';
        $default_date = date('Y-m-d', $game_obj->getDateUnixtime());
	}
}
?>
<div class="input-group">
	<input type="hidden" id="game-id" name="<?= $field_name ?>" value="<?= $field_value ?>">
	<input type="text" name="game-title" id="game-title" class="form-control" value="<?= $game_title ?>" readonly>
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" id="<?= $field_name ?>-ajax"><i class="glyphicon glyphicon-collapse-down"></i></button>
        <a id="game-goto-link" style="margin-left: 10px;" class="btn btn-default" href="/crud/edit/%5CSkif%5CStats%5CGame/<?php if(!empty($game_obj)) {echo $game_obj->getId();} ?>">перейти</a>
	</span>
</div>
<div id="select-game"></div>
<script>
	$("#<?= $field_name ?>-ajax").on("click", function (e) {
		e.preventDefault();
		if($(this).is(".open")) {
			$(this).removeClass("open").find(".glyphicon").removeClass("glyphicon-collapse-up").addClass("glyphicon-collapse-down");
			$("#select-game").html("");
			return;
		};
		$(this).addClass("open").find(".glyphicon").removeClass("glyphicon-collapse-down").addClass("glyphicon-collapse-up");

		$.post("/crud/widget/gameAttach", "game-date="+ "<?= $default_date ?>", function (data) {
			$("#select-game").html(data);
		});
	});
</script>