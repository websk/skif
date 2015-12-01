<?php

namespace Skif\Poll;

class PollController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Poll\Poll';

    public static function getBaseUrl($model_class_name)
    {
        return '/admin/poll';
    }

    public static function vote_add_query($vote_id)
    {
        $vote_label = isset($_REQUEST['vote_label']) ? intval($_REQUEST['vote_label']) : ''; // Код ответа по базе
        $vote_other = isset($_REQUEST['vote_other']) ? trim($_REQUEST['vote_other']) : ''; // Дополнительный ответ

        if ($_COOKIE['vote_access' . $vote_id] == 'no') {
            \Skif\Messages::setError('Вы уже проголосовали ранее!');
        } else if (!empty($vote_label)) {
            \Skif\DB\DBWrapper::query("UPDATE vote_list SET count=count+1 WHERE id='" . $vote_label . "'");

            setcookie('vote_access' . $vote_id, 'no', time() + 3600 * 24 * 365);
            \Skif\Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            if (!$vote_other) {
                \Skif\Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
            } else {
                $other = \Skif\DB\DBWrapper::readField("SELECT other FROM vote WHERE id='" . $vote_id . "'");
                if ($other == 0) {
                    \Skif\Messages::setError('В данном опросе нельзя вводить дополнительные ответы.');
                } else {
                    $res = \Skif\DB\DBWrapper::readAssoc("SELECT * FROM vote_other WHERE vote='" . $vote_id . "'");
                    $vote_tmp = 0;
                    foreach ($res as $k => $row) {
                        if (mb_strtolower($row['name']) == mb_strtolower($vote_other)) {
                            $vote_tmp = $row['id'];
                            break;
                        }
                    }

                    if ($vote_tmp == 0) {
                        \Skif\DB\DBWrapper::query("INSERT INTO vote_other SET vote=?, name=?", array($vote_id, $vote_other));
                    } else {
                        \Skif\DB\DBWrapper::query("UPDATE vote_other SET count=count+1 WHERE id=?", array($vote_tmp));
                    }

                    setcookie('vote_access' . $vote_id, 'no', time() + 3600 * 24 * 365);
                    \Skif\Messages::setMessage('Спасибо, ваш голос учтен!');
                }
            }
        }

        \Skif\Http::redirect($_SERVER['SCRIPT_NAME'] . '?cmd=result&vote_id=' . $vote_id);
    }


    // Результаты опроса

    public static function vote_show_result($vote_id)
    {
        $row_vote = \Skif\DB\DBWrapper::readAssocRow("SELECT name, other FROM vote WHERE id='" . $vote_id . "'");
        $vote_other = $row_vote['other'];

        $s = '';
        $sum = \Skif\DB\DBWrapper::readField("SELECT SUM(count) AS al FROM vote_list WHERE vote='" . $vote_id . "'");
        $res = \Skif\DB\DBWrapper::readAssoc("SELECT * FROM vote_list WHERE vote='" . $vote_id . "'");
        foreach ($res as $k => $row) {
            $vote_tmp = round($row['count'] / $sum * 100);
            $s .= '<tr>
			<td width="200" align="right">' . $row['name'] . '</td>
			<td width="280"><img src="/assets/images/img.gif" width="' . $vote_tmp . '" height="10" border="0"> ' . $vote_tmp . '%</td>
			</tr>';
        }

        $r = '<h2>' . $row_vote['name'] . '</h2>
	<table border="0">' . $s . '</table>
	<p>Всего проголосовало: ' . $sum . ' человек.</p>';

        if ($vote_other == '1') {
            $r .= '<div><a href="' . $_SERVER['SCRIPT_NAME'] . '?cmd=other&amp;vote_id=' . $vote_id . '">Другие варианты ответов этого опроса</a></div>';
        }

        $r .= '<p><a href="' . $_SERVER['SCRIPT_NAME'] . '">Список всех опросов</a></p>';

        return $r;
    }
}