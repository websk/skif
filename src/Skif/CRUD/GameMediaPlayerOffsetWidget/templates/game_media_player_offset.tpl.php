<?php
/**
 * @var $field_name
 * @var $field_value
 */
$player_offset = '';
if( $field_value != '' )
{
    $hours = floor($field_value/3600);
    $minutes = ($field_value/3600 - $hours)*60;
    $seconds = ceil(($minutes - floor($minutes))*60);
    $minutes = floor($minutes);

    $player_offset = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
?>
<div class="input-group">
	<input type="hidden" id="player-offset-sec" name="<?= $field_name ?>" value="<?= $field_value ?>">
	<input type="text" name="player-offset" id="player-offset" class="form-control" value="<?= $player_offset ?>">
</div>
<div id="select-game"></div>
<script>
    $(function() {
        $("#player-offset").on("change, keyup",  setOffsetSeconds);
    });
    function setOffsetSeconds() {
        var player_offset = $("#player-offset").val();
        var offset_arr = player_offset.split(':');
        var player_offset_seconds = parseInt(offset_arr[0])*3600+parseInt(offset_arr[1])*60+parseInt(offset_arr[2]);

        $("#player-offset-sec").val(player_offset_seconds);
    }

</script>