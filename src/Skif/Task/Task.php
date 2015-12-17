<?php
namespace Skif\Task;

class Task implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceGetTitle
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'task';

    const TASK_STATUS_NEW = 0;
    const TASK_STATUS_INPROGRESS = 1;
    const TASK_STATUS_DEFERRED = 2;
    const TASK_STATUS_FINISHED = 3;

    protected $id;
    protected $title = '';
    protected $created_date;
    protected $created_user_id;
    protected $description_task = '';
    protected $comment_in_task = '';
    protected $assigned_to_user_id;
    protected $last_modified_time;
    protected $status;

    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить задачу';

    public static $crud_model_class_screen_name = 'Задача';
    public static $crud_model_title_field = 'title';

    public static $crud_field_titles_arr = array(
        'title' => 'Название задачи',
        'created_date' => 'Дата создания',
        'description_task' => 'Описание',
        'comment_in_task' => 'Комментарии',
        'created_user_id' => 'Создал',
        'assigned_to_user_id' => 'Назначено на',
        'status' => 'Статус',
    );

    public static $crud_model_class_screen_name_for_list = 'Задачи';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'title' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'status' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static function crud_editorFieldsArr()
    {
        $disable_created_user_id = true;

        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            $disable_created_user_id = false;
        }

        $crud_editor_fields_arr = array(
            'title' => array('required' => true),
            'created_date' => array(
                'widget' => array('\Skif\CRUD\DatepickerWidget\DatepickerWidget', 'renderWidget'),
                'widget_settings' => array(
                    'date_format' => 'YYYY-MM-DD',
                    'default_value' => date('Y-m-d')
                ),
            ),
            'description_task' => array('widget' => 'textarea'),
            'comment_in_task' => array('widget' => 'textarea'),
            'created_user_id' => array(
                'widget' => array('\Skif\CRUD\UserWidget\UserWidget', 'renderWidget'),
                'widget_settings' => array(
                    'filtered_user_role_id' => \Skif\Users\UsersUtils::getRoleIdByDesignation('TASK_MANAGEMENT'),
                    'disabled' => $disable_created_user_id,
                    'default_value' => \Skif\Users\AuthUtils::getCurrentUserId()
                )
            ),
            'assigned_to_user_id' => array(
                'widget' => array('\Skif\CRUD\UserWidget\UserWidget', 'renderWidget'),
                'widget_settings' => array(
                    'filtered_user_role_id' => \Skif\Users\UsersUtils::getRoleIdByDesignation('TASK_MANAGEMENT')
                ),
            ),
            'status' => array(
                'widget' => 'options',
                'options_arr' => array(
                    self::TASK_STATUS_NEW => 'новая',
                    self::TASK_STATUS_INPROGRESS => 'в работе',
                    self::TASK_STATUS_DEFERRED => 'отложенная',
                    self::TASK_STATUS_FINISHED => 'выполненная',
                )
            ),
        );

        return $crud_editor_fields_arr;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCreatedTime()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_time
     */
    public function setCreatedTime($created_time)
    {
        $this->created_date = $created_time;
    }

    /**
     * @return mixed
     */
    public function getDescriptionTask()
    {
        return $this->description_task;
    }

    /**
     * @param mixed $description_task
     */
    public function setDescriptionTask($description_task)
    {
        $this->description_task = $description_task;
    }

    /**
     * @return mixed
     */
    public function getCommentInTask()
    {
        return $this->comment_in_task;
    }

    /**
     * @param mixed $comment_in_task
     */
    public function setCommentInTask($comment_in_task)
    {
        $this->comment_in_task = $comment_in_task;
    }

    /**
     * @return mixed
     */
    public function getAssignedToUserId()
    {
        return $this->assigned_to_user_id;
    }

    /**
     * @param mixed $assigned_to_user_id
     */
    public function setAssignedToUserId($assigned_to_user_id)
    {
        $this->assigned_to_user_id = $assigned_to_user_id;
    }

    /**
     * @return mixed
     */
    public function getLastModifiedTime()
    {
        return $this->last_modified_time;
    }

    /**
     * @param mixed $last_modified_time
     */
    public function setLastModifiedTime($last_modified_time)
    {
        $this->last_modified_time = $last_modified_time;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreatedUserId()
    {
        return $this->created_user_id;
    }

    /**
     * @param mixed $created_user_id
     */
    public function setCreatedUserId($created_user_id)
    {
        $this->created_user_id = $created_user_id;
    }

    public function save()
    {
        if (!$this->getId()) {
            $this->setCreatedTime(date('Y-m-d H:i:s'));
        }

        $this->setLastModifiedTime(date('Y-m-d H:i:s'));

        \Skif\Util\ActiveRecordHelper::saveModelObj($this);

        self::afterUpdate($this->getId());
    }

}