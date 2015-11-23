<?php
$game_date = date('Ymd', time()); // default value
$default_date = date('Y-m-d', time());

if (array_key_exists('game-date', $_REQUEST)) {
	if ($_REQUEST['game-date'] != '') {
		$game_date = preg_replace('@-@', '', $_REQUEST['game-date']);
		$default_date = $_REQUEST['game-date'];
	}
}

$games_ids_arr = \Skif\Stats\StatsOnlineHelper::getGamesIdsArrOld($default_date);

$table_game = '';
$prev_tournament_id = null;

foreach ($games_ids_arr as $game_id)
{
	$game_obj = \Skif\Stats\GameFactory::loadGame($game_id);
    if (!$game_obj) {
        continue;
    }

    $tournament_id = $game_obj->getTurnirId();

    $tournament_obj = \Skif\Stats\TurnirFactory::loadTurnir($tournament_id);
    \Skif\Utils::assert($tournament_obj);

    if ($prev_tournament_id != $tournament_id)
    {
        $prev_tournament_id = $tournament_id;

        $sport_obj = \Skif\Stats\SportFactory::loadSport($game_obj->getSportId());
        $sport_name = ($sport_obj) ? $sport_obj->getName() : 'Без спорта';

        $table_game .= '<tr><th colspan="3" align="center">'.$sport_name.' / '.$tournament_obj->getFullName().'</th></tr>';

        // Соревнования. Даем ссылку на все соревнование целиком.
        if ($tournament_obj->isNotGamingTournament()) {
            $tour_obj = \Skif\Stats\TourFactory::loadTour($tournament_id, $game_obj->getTour());

            $table_game .= '<tr class="selectable" data-title="' . $game_obj->getHeadTitle() . '. ' . $tour_obj->getName() . '" data-gameid="' . $tour_obj->getBaseGameIdForNotGamingTournament() . '">';
            $table_game .= '<td align="center" colspan="3">' . $game_obj->getHeadTitle() . '. ' . $tour_obj->getName() . '</td>';
            $table_game .= '</tr>';
        }
    }

    if ($tournament_obj->isNotGamingTournament()) {
        continue;
    }

    // Игровые турниры

    $side1_obj = null;
    $side2_obj = null;

    $side1_obj_two = null;
    $side2_obj_two = null;

    if (($tournament_obj->checkFormat(\Skif\Constants::TURNIR_TYPE_PAIRS))
        && ($tournament_obj->checkFormat(\Skif\Constants::TURNIR_TYPE_INDIVIDUAL))
    ) {
        $side1_pair_obj = $game_obj->getSideFirstObj();
        if (($side1_pair_obj) && ($side1_pair_obj instanceof \Skif\Stats\PlayersPair)) {
            $side1_id = $side1_pair_obj->getPlayerFirstId();
            $side1_obj = \Skif\Stats\PlayerFactory::loadPlayer($side1_id);
            $side1_id_two = $side1_pair_obj->getPlayerSecondId();
            $side1_obj_two = \Skif\Stats\PlayerFactory::loadPlayer($side1_id_two);
        }

        $side2_pair_obj = $game_obj->getSideSecondObj();
        if (($side2_pair_obj) && ($side2_pair_obj instanceof \Skif\Stats\PlayersPair)) {
            $side2_id = $side2_pair_obj->getPlayerFirstId();
            $side2_obj = \Skif\Stats\PlayerFactory::loadPlayer($side2_id);
            $side2_id_two = $side2_pair_obj->getPlayerSecondId();
            $side2_obj_two = \Skif\Stats\PlayerFactory::loadPlayer($side2_id_two);
        }
    } else {
        $side1_obj = $game_obj->getSideFirstObj();
        $side2_obj = $game_obj->getSideSecondObj();
    }


    $player1_two = '';
    $player2_two = '';
    if ($side1_obj_two) {
        $player1_two = ', ' . $side1_obj_two->getName();
    }
    if ($side2_obj_two) {
        $player2_two = ', ' . $side2_obj_two->getName();
    }

	$table_game .= '<tr class="selectable" data-title="'.$game_obj->getHeadTitle().'" data-gameid="'.$game_obj->getId().'">';
    $table_game .= '<td align="right" width="47%">'. ($side1_obj ? $side1_obj->getName() . $player1_two : '') .'</td>';
    $table_game .= '<td align="center">' . $game_obj->renderResult() . '</td>';
    $table_game .= '<td align="left" width="47%">'. ($side2_obj ? $side2_obj->getName() . $player2_two : '') .'</td>';
    $table_game .= '</tr>';
}

?>
<style>.selectable {cursor: pointer;}</style>
<br>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="row">
			<div class="col-sm-6">
			<div class="form-group">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="control-label">Доступные матчи:</label>
			</div>
		</div>
			<div class="col-sm-6">
			<div class="input-group">
				<input type="text" class="form-control" value="<?= $default_date ?>" id="datepicker" data-date-format="yyyy-mm-dd">
			</div>
		</div>
		</div>
	</div>
	<table class="table table-hover">
		<?= $table_game ?>
	</table>
</div>
<script>
(function() {
    function refresh() {
        $.post("/crud/widget/gameAttach", "game-date="+ $("#datepicker").val() , function (data) {
            $("#select-game").html(data);
        });
    }

    $("#datepicker").datepicker({weekStart: 1}).on("changeDate", function() {
        refresh();
        $(this).datepicker("hide");
    });

    $(".selectable").click(function() {
        $("#game-id").val($(this).attr("data-gameid"));
        $("#game-title").val($(this).attr("data-title") + " (" + $(this).attr("data-gameid") + ")");
        $("#game_id-ajax").removeClass("open").find(".glyphicon").removeClass("glyphicon-collapse-up").addClass("glyphicon-collapse-down");
        $("#select-game").html("");
    });
})();
</script>
