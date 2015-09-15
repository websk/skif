<?php

namespace Skif\Logger;


class ControllerLogger
{

    public function listAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $logger_objs_arr = \Skif\DB\DBWrapper::readObjects(
            "SELECT entity_id FROM admin_log GROUP BY entity_id"
        );

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Logger', 'list.tpl.php', array(
                'logger_objs_arr' => $logger_objs_arr
            )
        );

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => 'Logger',
                'content' => $html
            )
        );
    }

    public function object_logAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $logger_objs_id = urldecode(\Skif\Utils::url_arg(3));

        $logger_objs_arr = \Skif\DB\DBWrapper::readObjects(
            "SELECT id, user_id, action, ts, ip FROM admin_log WHERE entity_id = ? ORDER BY ts DESC",
            array($logger_objs_id)
        );

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule('Logger', 'object_log.tpl.php', array(
                'logger_objs_arr' => $logger_objs_arr
            )
        );

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => 'История "' . $logger_objs_id . '"',
                'content' => $html
             )
        );
    }

    /*
    static public function lite_object_logAction($obj)
    {
        // Проверка прав доступа
        \Skif\Helpers::exit404If(!\Skif\User\DrupalUserFactory::currentUserHasRoles(
            array(\Skif\User\DrupalUser::ROLE_ADMIN)
        ));

        $entity_id = \Skif\Helpers::getFullObjectId($obj);

        $logger_objs_arr = \Skif\DB\DBWrapper::readObjects(
            "SELECT id, user_id, action, ts FROM admin_log WHERE entity_id = ? ORDER BY ts DESC",
            array($entity_id)
        );

        $html = \Skif\PhpTemplate::template('Skif/Logger/views/object_log.tpl.php', array(
                'logger_objs_arr' => $logger_objs_arr
            )
        );

        return $html;
    }
    */

    public function recordAction()
    {
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $record_id = \Skif\Utils::url_arg(3);

        $html = '';

        $html .= self::renderRecordHead($record_id);
        $html .= self::delta($record_id);
        $html .= self::renderObjectFields($record_id);

        $record_obj = \Skif\DB\DBWrapper::readObject(
            "SELECT user_id, ts, ip, action, entity_id, object FROM admin_log WHERE id = ?",
            array($record_id)
        );

        if (!$record_obj) {
            return 'missing record';
        }

        echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
                'title' => 'Запись ' . $record_obj->ts,
                'content' => $html,
                'breadcrumbs_arr' => array('История' => '/admin/logger/object_log/' . urlencode($record_obj->entity_id))
            )
        );

    }

    static public function renderRecordHead($record_id)
    {
        $record_obj = \Skif\DB\DBWrapper::readObject(
            "SELECT user_id, ts, ip, action, entity_id, object FROM admin_log WHERE id = ?",
            array($record_id)
        );


        $user_obj = \Skif\Users\User::factory($record_obj->user_id);

        $username = $user_obj ? $user_obj->getName() : '';


        return '
<dl class="dl-horizontal jumbotron" style="margin-top:20px;padding: 10px;">
	<dt style="padding: 5px 0;">Имя пользователя</dt>
	<dd style="padding: 5px 0;">' . $username . '</dd>
    <dt style="padding: 5px 0;">Время изменения</dt>
    <dd style="padding: 5px 0;">' . $record_obj->ts . '</dd>
    <dt style="padding: 5px 0;">IP адрес</dt>
    <dd style="padding: 5px 0;">' . $record_obj->ip . '</dd>
    <dt style="padding: 5px 0;">Тип изменения</dt>
    <dd style="padding: 5px 0;">' . $record_obj->action . '</dd>
    <dt style="padding: 5px 0;">Идентификатор</dt>
    <dd style="padding: 5px 0;">' . $record_obj->entity_id . '</dd>
</dl>
   ';
    }

    static public function renderObjectFields($record_id)
    {
        $html = '<h2>Все поля объекта</h2>';

        $logger_objs_arr = \Skif\DB\DBWrapper::readObject(
            "SELECT user_id, ts, ip, action, entity_id, object FROM admin_log WHERE id = ?",
            array($record_id)
        );

        $record_objs = unserialize($logger_objs_arr->object);

        $value_as_list = self::convertValueToList($record_objs);
        ksort($value_as_list); // сортируем для красоты

        //$html .= '<table class="table">';
        $last_path = '';

        foreach ($value_as_list as $path => $value) {
            $path_to_display = $path;

            if (self::getPathWithoutLastElement($last_path) == self::getPathWithoutLastElement($path)) {
                $elems = explode('.', $path);
                $last_elem = array_pop($elems);
                if (count($elems)) {
                    $path_to_display = '<span style="color: #999">' . implode('.', $elems) . '</span>.' . $last_elem;
                }
            }

            /*
            $html .= '<tr>';
            $html .= '<td>' . $path_to_display . '</td>';
            $html .= '<td><pre style="white-space: pre-wrap;">' . $value . '</pre></td>';
            $html .= '</tr>';
            */

            if (strlen($value) > 100){
                $html .= '<div style="padding: 5px 0px; border-bottom: 1px solid #ddd;">';

                $html .= '<div><b>' . $path_to_display . '</b></div>';
                $html .= '<div><pre style="white-space: pre-wrap;">' . $value . '</pre></div>';
                $html .= '</div>';
            } else {
                $html .= '<div style="padding: 5px 0px; border-bottom: 1px solid #ddd;">';

                $html .= '<span style="padding-right: 50px;"><b>' . $path_to_display . '</b></span>';
                $html .= $value;
                $html .= '</div>';
            }


            $last_path = $path;
        }
        //$html .= '</table>';

        return $html;
    }

    static public function getPathWithoutLastElement($path)
    {
        $elems = explode('.', $path);
        array_pop($elems);
        return implode('.', $elems);
    }

    static public function delta($current_record_id)
    {
        $html = '';

        $current_record_obj = \Skif\DB\DBWrapper::readObject(
            "SELECT id, user_id, ts, ip, action, entity_id, object FROM admin_log WHERE id = ?",
            array($current_record_id)
        );

        if (!$current_record_obj) {
            return 'не найден объект текущей записи';
        }

        // находим предыдущую запись лога для этого объекта

        $prev_record_obj = \Skif\DB\DBWrapper::readObject(
            "SELECT id, user_id, ts, ip, action, entity_id, object FROM admin_log WHERE id < ? AND entity_id = ? ORDER BY id DESC LIMIT 1",
            array($current_record_id, $current_record_obj->entity_id)
        );

        if (!$prev_record_obj) {
            return '<div>Предыдущая запись истории для этого объекта не найдена.</div>';
        }

        // определение дельты

        $html .= '<h2>Изменения относительно <a href="/admin/logger/record/' . $prev_record_obj->id . '">предыдущей версии</a></h2>';

        $current_obj = unserialize($current_record_obj->object);
        $prev_obj = unserialize($prev_record_obj->object);

        $current_record_as_list = self::convertValueToList($current_obj);
        ksort($current_record_as_list); // сортируем для красоты
        $prev_record_as_list = self::convertValueToList($prev_obj);
        ksort($prev_record_as_list); // сортируем для красоты

        $html .= '<table class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Поле</th>';
        $html .= '<th>Старое значение</th>';
        $html .= '<th>Новое значение</th>';
        $html .= '</tr>';
        $html .= '</thead>';

        $added_rows = array_diff_key($current_record_as_list, $prev_record_as_list);

        foreach ($added_rows as $k => $v) {
            $html .= '<tr>';
            $html .= '<td><b>' . $k . '</b></td>';
            $html .= '<td style="background-color: #eee;"></td>';
            $html .= '<td>' . self::renderDeltaValue($v) . '</td>';
            $html .= '</tr>';
        }

        $deleted_rows = array_diff_key($prev_record_as_list, $current_record_as_list);

        foreach ($deleted_rows as $k => $v) {
            $html .= '<tr>';
            $html .= '<td><b>' . $k . '</b></td>';
            $html .= '<td>' . self::renderDeltaValue($v) . '</td>';
            $html .= '<td style="background-color: #eee;"></td>';
            $html .= '</tr>';
        }

        foreach ($current_record_as_list as $k => $current_v) {
            if (array_key_exists($k, $prev_record_as_list)) {
                $prev_v = $prev_record_as_list[$k];
                if ($current_v != $prev_v) {
                    $html .= '<tr>';
                    $html .= '<td><b>' . $k . '</b></td>';
                    $html .= '<td>' . self::renderDeltaValue($prev_v) . '</td>';
                    $html .= '<td>' . self::renderDeltaValue($current_v) . '</td>';
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</table>';

        $html .= '<div>Для длинных значений полный текст здесь не приведен, его можно увидеть в полях объекта ниже.</div>';

        return $html;
    }

    static public function renderDeltaValue($v)
    {
        $limit = 300;

        if (strlen($v) < $limit) {
            return $v;
        }

        return mb_substr($v, 0, $limit) . '...';
    }

    static public function convertValueToList($value_value, $value_path = '')
    {
        if (is_null($value_value)) {
            return array($value_path => '#NULL#');
        }

        if (is_scalar($value_value)) {
            return array($value_path => htmlentities($value_value));
        }

        $value_as_array = null;
        $output_array = array();

        if (is_array($value_value)) {
            $value_as_array = $value_value;
        }

        if (is_object($value_value)) {
            $value_as_array = array();

            foreach ($value_value as $property_name => $property_value) {
                $value_as_array[$property_name] = $property_value;
            }

            $reflect = new \ReflectionClass($value_value);
            $properties = $reflect->getProperties();

            foreach ($properties as $prop_obj) {
                // не показываем статические свойства класса - они не относятся к конкретному объекту (например, это могут быть настройки круда для класса) и в журнале не нужны
                if ($prop_obj->isStatic()) {
                    continue;
                }

                $prop_obj->setAccessible(true);
                $name = $prop_obj->getName();
                $value = $prop_obj->getValue($value_value);
                $value_as_array[$name] = $value;
            }
        }

        if (!is_array($value_as_array)) {
            throw new \Exception('Не удалось привести значение к массиву');
        }

        foreach ($value_as_array as $key => $value) {
            $key_path = $key;
            if ($value_path != '') {
                $key_path = $value_path . '.' . $key;
            }

            $value_output = self::convertValueToList($value, $key_path);
            $output_array = array_merge($output_array, $value_output);
        }

        return $output_array;
    }


}
