<?php

namespace WebSK\Skif\Logger\RequestHandlers;

use OLOG\HTML;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\AdminRender;
use WebSK\UI\LayoutDTO;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersRoutes;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Utils\HTTP;
use WebSK\UI\BreadcrumbItemDTO;
use WebSK\Skif\Logger\LoggerConstants;
use WebSK\Skif\Logger\LoggerRoutes;
use WebSK\Skif\Logger\LoggerServiceProvider;

/**
 * Class EntryEditHandler
 * @package WebSK\Skif\Logger\RequestHandlers
 */
class EntryEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $entry_id
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, int $entry_id): ResponseInterface
    {
        $entry_obj = LoggerServiceProvider::getEntryService($this->container)->getById($entry_id, false);
        if (!$entry_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $html = '';
        $html .= $this->renderRecordHead($entry_id);
        $html .= $this->delta($entry_id);
        $html .= $this->renderObjectFields($entry_id);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle(date('Y.d.m H:i', $entry_obj->getCreatedAtTs()));
        $layout_dto->setContentHtml($html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', LoggerConstants::ADMIN_ROOT_PATH),
            new BreadcrumbItemDTO(
                'Журналы',
                $this->pathFor(LoggerRoutes::ROUTE_NAME_ADMIN_LOGGER_ENTRIES_LIST)
            ),
            new BreadcrumbItemDTO(
                $entry_obj->getObjectFullId(),
                $this->pathFor(
                    LoggerRoutes::ROUTE_NAME_ADMIN_LOGGER_OBJECT_ENTRIES_LIST,
                    ['object_full_id' => urlencode($entry_obj->getObjectFullId())]
                )
            ),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return AdminRender::renderLayout($response, $layout_dto);
    }

    /**
     * @param $current_record_id
     * @return string
     */
    public function delta($current_record_id)
    {
        $html = '';

        $current_record_obj = LoggerServiceProvider::getEntryService($this->container)->getById($current_record_id);

        // находим предыдущую запись лога для этого объекта

        $prev_record_id = LoggerServiceProvider::getEntryService($this->container)
            ->getPrevRecordEntryId($current_record_id);

        if (!$prev_record_id) {
            return '<div>Предыдущая запись истории для этого объекта не найдена.</div>';
        }

        $prev_record_obj = LoggerServiceProvider::getEntryService($this->container)->getById($prev_record_id);


        $edit_url = $this->pathFor(LoggerRoutes::ROUTE_NAME_ADMIN_LOGGER_ENTRY_EDIT, ['entry_id' => $prev_record_id]);

        // определение дельты

        $html .= '<h2>Изменения относительно <a href="' . $edit_url . '">предыдущей версии</a></h2>';

        $current_obj = unserialize($current_record_obj->getSerializedObject());
        $prev_obj = unserialize($prev_record_obj->getSerializedObject());

        $current_record_as_list = $this->convertValueToList($current_obj);
        ksort($current_record_as_list); // сортируем для красоты
        $prev_record_as_list = $this->convertValueToList($prev_obj);
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
            $html .= '<td>' . $this->renderDeltaValue($v) . '</td>';
            $html .= '</tr>';
        }

        $deleted_rows = array_diff_key($prev_record_as_list, $current_record_as_list);

        foreach ($deleted_rows as $k => $v) {
            $html .= '<tr>';
            $html .= '<td><b>' . $k . '</b></td>';
            $html .= '<td>' . $this->renderDeltaValue($v) . '</td>';
            $html .= '<td style="background-color: #eee;"></td>';
            $html .= '</tr>';
        }

        foreach ($current_record_as_list as $k => $current_v) {
            if (array_key_exists($k, $prev_record_as_list)) {
                $prev_v = $prev_record_as_list[$k];
                if ($current_v != $prev_v) {
                    $html .= '<tr>';
                    $html .= '<td><b>' . $k . '</b></td>';
                    $html .= '<td>' . $this->renderDeltaValue($prev_v) . '</td>';
                    $html .= '<td>' . $this->renderDeltaValue($current_v) . '</td>';
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</table>';

        $html .= '<div>Для длинных значений полный текст здесь не приведен, его можно увидеть в полях объекта ниже.</div>';

        return $html;
    }

    /**
     * @param $v
     * @return string
     */
    protected function renderDeltaValue($v)
    {
        $limit = 300;

        if (strlen($v) < $limit) {
            return $v;
        }

        return mb_substr($v, 0, $limit) . '...';
    }

    /**
     * @param $user_full_id
     * @return string
     */
    protected function getUserNameWithLinkByFullId($user_full_id)
    {
        $user_str = $user_full_id;
        $user_full_id_arr = explode('.', $user_str);
        if (!array_key_exists(1, $user_full_id_arr)) {
            return $user_str;
        }

        $user_obj = UsersServiceProvider::getUserService($this->container)->getById($user_full_id_arr[1], false);
        if (is_null($user_obj)) {
            return $user_str;
        }

        $user_edit_url = $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);

        return HTML::a($user_edit_url, $user_obj->getName());
    }

    /**
     * @param $record_id
     * @return string
     */
    protected function renderRecordHead($record_id)
    {
        $entry_obj = LoggerServiceProvider::getEntryService($this->container)->getById($record_id);

        $user_str = $this->getUserNameWithLinkByFullId($entry_obj->getUserFullid());

        return '<dl class="dl-horizontal jumbotron" style="margin-top:20px;padding: 10px;">
	<dt style="padding: 5px 0;">Имя пользователя</dt>
	<dd style="padding: 5px 0;">' . $user_str . '</dd>
    <dt style="padding: 5px 0;">Время изменения</dt>
    <dd style="padding: 5px 0;">' . date('d.m H:i', $entry_obj->getCreatedAtTs()) . '</dd>
    <dt style="padding: 5px 0;">IP адрес</dt>
    <dd style="padding: 5px 0;">' . $entry_obj->getUserIp() . '</dd>
    <dt style="padding: 5px 0;">Комментарий</dt>
    <dd style="padding: 5px 0;">' . $entry_obj->getComment() . '</dd>
    <dt style="padding: 5px 0;">Идентификатор</dt>
    <dd style="padding: 5px 0;">' . $entry_obj->getObjectFullid() . '</dd>
</dl>
   ';
    }

    /**
     * @param $record_id
     * @return string
     * @throws \ReflectionException
     */
    protected function renderObjectFields($record_id)
    {
        $html = '<h2>Все поля объекта</h2>';

        $record_obj = LoggerServiceProvider::getEntryService($this->container)->getById($record_id);

        $record_objs = unserialize($record_obj->getSerializedObject());

        $value_as_list = $this->convertValueToList($record_objs);
        ksort($value_as_list); // сортируем для красоты

        //$html .= '<table class="table">';
        $last_path = '';

        foreach ($value_as_list as $path => $value) {
            $path_to_display = $path;

            if ($this->getPathWithoutLastElement($last_path) == $this->getPathWithoutLastElement($path)) {
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

            if (strlen($value) > 100) {
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

    /**
     * @param $value_value
     * @param string $value_path
     * @return array
     * @throws \ReflectionException
     */
    protected function convertValueToList($value_value, $value_path = '')
    {
        if (is_null($value_value)) {
            return array($value_path => '#NULL#');
        }

        if (is_scalar($value_value)) {
            return array($value_path => htmlentities($value_value));
        }

        $value_as_array = null;
        $output_array = [];

        if (is_array($value_value)) {
            $value_as_array = $value_value;
        }

        if (is_object($value_value)) {
            $value_as_array = [];

            foreach ($value_value as $property_name => $property_value) {
                $value_as_array[$property_name] = $property_value;
            }

            $reflect = new \ReflectionClass($value_value);
            $properties = $reflect->getProperties();

            foreach ($properties as $prop_obj) {
                // не показываем статические свойства класса - они не относятся к конкретному объекту
                // (например, это могут быть настройки круда для класса) и в журнале не нужны
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

            $value_output = $this->convertValueToList($value, $key_path);
            $output_array = array_merge($output_array, $value_output);
        }

        return $output_array;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getPathWithoutLastElement($path)
    {
        $elems = explode('.', $path);
        array_pop($elems);
        return implode('.', $elems);
    }
}
