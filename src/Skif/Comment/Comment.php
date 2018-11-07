<?php

namespace Skif\Comment;


use WebSK\Skif\Users\User;
use Websk\Utils\Assert;

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
    protected $parent_id = 0;
    protected $comment;
    protected $url;
    protected $user_id = null;
    protected $user_name;
    protected $user_email;
    protected $date_time;
    protected $children_ids_arr;
    protected $url_md5;

    public function __construct()
    {
        $this->user_id = \WebSK\Skif\Users\AuthUtils::getCurrentUserId();
        $this->date_time = date('Y-m-d H:i:s');
    }


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
        'parent_id' => 'Ответ к комментарию',
    );

    public static $crud_model_class_screen_name_for_list = 'Комментарии';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'comment' => array('col_class' => 'col-md-4 col-sm-6 col-xs-6'),
        'user_name' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        'date_time' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_default_context_arr_for_list = array('parent_id' => 0);

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
        'date_time' => array(
            'widget' => array('\Skif\CRUD\DatepickerWidget\DatepickerWidget', 'renderWidget'),
            'widget_settings' => array(
                'date_format' => 'YYYY-MM-DD HH:mm:ss'
            ),
        ),
        'comment' => array('widget' => 'textarea'),
    );

    public static $crud_fast_create_field_name = 'comment';

    public static $related_models_arr = array(
        '\Skif\Comment\Comment' => array(
            'link_field' => 'parent_id',
            'field_name' => 'children_ids_arr',
            'list_title' => 'Ответы',
            'context_fields_arr' => array('url', 'url_md5'),
        )
    );

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
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
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
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrlMd5()
    {
        return $this->url_md5;
    }

    /**
     * @param mixed $url_md5
     */
    public function setUrlMd5($url_md5)
    {
        $this->url_md5 = $url_md5;
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
     * @param null $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Имя незарегистрированного пользователя
     * @return string
     */
    public function getUserName()
    {
        if ($this->user_id) {
            $user_obj = User::factory($this->user_id);
            Assert::assert($user_obj);

            return $user_obj->getName();
        }

        return $this->user_name;
    }

    /**
     * @param mixed $user_name
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * Email незарегистрированного пользователя
     * @return string
     */
    public function getUserEmail()
    {
        if ($this->user_id) {
            $user_obj = User::factory($this->user_id);

            return $user_obj->getEmail();
        }

        return $this->user_email;
    }

    /**
     * @param mixed $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
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
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Время в формате unix time
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
        $this->date_time = $date_time;
    }

    /**
     * Ветка с ответами
     * @return mixed
     */
    public function getChildrenIdsArr()
    {
        return $this->children_ids_arr;
    }

    public static function afterUpdate($comment_id)
    {
        $comment_obj = \Skif\Comment\Comment::factory($comment_id);

        if ($comment_obj->getParentId()) {
            self::removeObjFromCacheById($comment_obj->getParentId());

            if (\WebSK\Skif\ConfWrapper::value('comments.send_answer_to_email')) {
                $parent_comment_obj = \Skif\Comment\Comment::factory($comment_obj->getParentId());
                if ($parent_comment_obj->getUserEmail()) {
                    $site_email = \WebSK\Skif\ConfWrapper::value('site_email');
                    $site_url = \WebSK\Skif\ConfWrapper::value('site_url');
                    $site_name = \WebSK\Skif\ConfWrapper::value('site_name');

                    $mail_message = 'Здравствуйте, ' . $parent_comment_obj->getUserEmail() . '!<br />';
                    $mail_message .= 'Получен ответ на ваше сообщение:<br />';
                    $mail_message .= $parent_comment_obj->getComment() . '<br />';
                    $mail_message .= 'Ответ: ' . $comment_obj->getComment() . '<br />';
                    $mail_message .= $site_name . ', ' . $site_url;

                    $subject = 'Ответ на сообщение на сайте' . $site_name;

                    $mail = new \PHPMailer;
                    $mail->CharSet = "utf-8";
                    $mail->setFrom($site_email, $site_name);
                    $mail->addAddress($parent_comment_obj->getUserEmail());
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $mail_message;
                    $mail->AltBody = \Skif\Utils::checkPlain($mail_message);
                    $mail->send();
                }
            }
        }

        self::removeObjFromCacheById($comment_id);
    }

    public function afterDelete()
    {
        $children_ids_arr = $this->getChildrenIdsArr();

        foreach ($children_ids_arr as $children_comment_id) {
            $children_comment_obj = \Skif\Comment\Comment::factory($children_comment_id);
            $children_comment_obj->delete();
        }

        self::removeObjFromCacheById($this->getParentId());

        self::removeObjFromCacheById($this->getId());
    }

}