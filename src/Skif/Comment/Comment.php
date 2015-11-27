<?php

namespace Skif\Comment;


class Comment  implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceGetTitle
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'comments';

    protected $id;
    protected $parent_id;
    protected $comment;
    protected $url;
    protected $user_id = null;
    protected $user_name;
    protected $user_email;
    protected $date_time;
    protected $children_ids_arr;
    protected $url_md5;

    public static $active_record_ignore_fields_arr = array(
        'children_ids_arr',
    );


    public static $crud_create_button_required_fields_arr = array('parent_id');
    public static $crud_create_button_title = 'Добавить комментарий';

    public static $crud_model_class_screen_name = 'Комментарий';
    public static $crud_model_title_field = 'id';

    public static $crud_field_titles_arr = array(
        'comment' => 'Комментарий',
        'user_name' => 'Пользователь',
        'user_email' => 'Email',
        'date_time' => 'Добавлено',
        'parent_id' => 'Родитель',
    );

    public static $crud_model_class_screen_name_for_list = 'Комментарии';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'comment' => array('col_class' => 'col-md-4 col-sm-6 col-xs-6'),
        'user_name' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        'date_time' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_default_context_arr_for_list = array('parent_id' => null);

    public static $crud_editor_fields_arr = array(
        'user_name' => array(),
        'user_email' => array(),
        'url' => array(),
        'parent_id' => array(
            'widget' => array('\Skif\CRUD\ModelReferenceWidget\ModelReferenceWidget', 'renderWidget'),
            'widget_settings' => array(
                'model_class_name' => '\Skif\Comment\Comment'
            )
        ),
        'comment' => array('widget' => 'textarea'),
    );

    public static $crud_fast_create_field_name = 'comment';

    public static $crud_related_models_arr = array(
        '\Skif\Comment\Comment' => array(
            'link_field' => 'parent_id',
            'list_title' => 'Ответы',
            'context_fields_arr' => array('url', 'url_md5'),
        )
    );

    public function getEditorTabsArr()
    {
        $tabs_obj_arr = array();
        $tabs_obj_arr[] = new \Skif\EditorTabs\Tab(\Skif\Comment\CommentController::getEditUrlForObj($this), self::$crud_model_class_screen_name);

        return $tabs_obj_arr;
    }

    public function load($id)
    {
        $is_loaded = \Skif\Util\ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $this->children_ids_arr = \Skif\Comment\CommentUtils::getCommentsIdsArrByUrl($this->getUrl(), 1, $this->getId());

        return true;
    }

    public function getTitle()
    {
        return 'Комментарий ' . $this->getId();
    }

    /**
     * ID
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Parent ID
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Page URL
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * User ID
     * @return null|int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Имя незарегистрированного пользователя
     * @return string
     */
    public function getUserName()
    {
        if ($this->user_id) {
            $user_obj = \Skif\Users\User::factory($this->user_id);
            \Skif\Utils::assert($user_obj);

            return $user_obj->getName();
        }

        return $this->user_name;
    }

    /**
     * Email незарегистрированного пользователя
     * @return string
     */
    public function getUserEmail()
    {
        if ($this->user_id) {
            $user_obj = \Skif\Users\User::factory($this->user_id);

            return $user_obj->getEmail();
        }

        return $this->user_email;
    }

    /**
     * Комментарий
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Время в вормате unix time
     * @return int
     */
    public function getUnixTime()
    {
        return strtotime($this->date_time);
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->date_time;
    }

    /**
     * @param mixed $date_time
     */
    public function setDateTime($date_time)
    {
        if (!$date_time) {
            $date_time = date('Y-m-d H:i:s');
        }

        $this->date_time = $date_time;
    }

    /**
     * Удаление комментария
     * @return bool
     */
    public function delete()
    {
        $query = "DELETE FROM comments WHERE parent_id=?";
        \Skif\DB\DBWrapper::query($query, array($this->id));

        $query = "DELETE FROM comments WHERE id=?";
        \Skif\DB\DBWrapper::query($query, array($this->id));

        \Skif\Factory::removeObjectFromCache('\Skif\Comment\Comment', $this->id);
        \Skif\Factory::removeObjectFromCache('\Skif\Comment\Comment', $this->parent_id);

        return true;
    }

    /**
     * Ветка с ответами
     * @return mixed
     */
    public function getChildrenIdsArr()
    {
        return $this->children_ids_arr;
    }

}