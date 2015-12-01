<?php

namespace Skif\Poll;


class PollUtils
{

    function vote($vote_id = "")
    {
        if ($vote_id > 0) {
            $row = \Skif\DB\DBWrapper::readAssocRow("SELECT * FROM vote WHERE id=?" , array($vote_id));
        }
        else {
            $row = \Skif\DB\DBWrapper::readAssocRow("SELECT * FROM vote WHERE main='1' LIMIT 0, 1"); //Поиск индексного опроса

            if (!$row) {
                $row = \Skif\DB\DBWrapper::readAssocRow("SELECT * FROM vote LIMIT 0, 1");
            } // В случае отсутствия индексного, выводить первый
        }
        if ($row['name'] == '') {
            return "Действующие опросы отсутствуют.";
        }

        $vote_tmp = $row['id'];
        $vote_other = $row['other'];

        $content = '<div>' . $row['name'] . '</div>';

        $content .= '<form action="/vote.php" method="post">';

        $res = \Skif\DB\DBWrapper::readAssoc("SELECT * FROM vote_list WHERE vote=? ORDER BY id", array($vote_tmp));
        foreach ($res as $k => $row) {
            $content .= '<div class="radio">
                <label>
                    <input type="radio" name="vote_label" value=' . $row['id'] . '>' . $row['name'] . '
                </label>
            </div>';
        }

        if ($vote_other == 1) {
            $content .= '<div class="form-group">
            <label>Ваш ответ</label>
            <input type="text" name="vote_other" class="form-control input-sm">
        </div>';
        }

        $content .= '
        <input type="hidden" name="cmd" value="vote"><input type="hidden" name="vote_id" value="' . $vote_tmp . '">
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <input type="submit" value="Голосовать" class="btn btn-default btn-sm">
                </div>
                <div class="col-md-6 text-right">
                    <a href="/vote.php?cmd=result&vote_id=' . $vote_tmp . '">Результаты</a>
                </div>
            </div>
        </div>
        </form>';

        return $content;
    }

}