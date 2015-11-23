<?php

namespace Skif\CRUD\GameMediaPlayerOffsetWidget;


class GameMediaPlayerOffsetWidget
{
    public static function renderWidget($field_name, $field_value)
    {
        $html = \Skif\PhpTemplate::renderTemplate('game_media_player_offset.tpl.php', array(
                'field_name' => $field_name,
                'field_value' => $field_value
            )
        );

        return $html;
    }

} 