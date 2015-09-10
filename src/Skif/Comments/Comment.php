<?php

namespace Skif\Comments;


class Comment
{
    protected $id;
    protected $parent_id;
    protected $url;
    protected $user_id = null;
    protected $user_name;
    protected $user_email;
    protected $comment;
    protected $date_time;
    protected $children_ids_arr;

    public function load($page_id)
    {
        $query = "SELECT id, parent_id, url, user_id, user_name, user_email, comment, date_time FROM comments WHERE id=?";
        $raw_obj = \Skif\DB\DBWrapper::readObject($query, array($page_id));

        if (!$raw_obj) {
            return false;
        }

        $object_vars_arr = get_object_vars($raw_obj);
        foreach ($object_vars_arr as $key => $value) {
            $this->$key = $value;
        }

        $this->children_ids_arr = \Skif\Comments\CommentsUtils::getCommentsIdsArrByUrl($this->getUrl(), 1, $this->getId());

        return true;
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
     * Удаление комментария
     * @return bool
     */
    public function delete()
    {
        $query = "DELETE FROM comments WHERE parent_id=?";
        \Skif\DB\DBWrapper::query($query, array($this->id));

        $query = "DELETE FROM comments WHERE id=?";
        \Skif\DB\DBWrapper::query($query, array($this->id));

        \Skif\Factory::removeObjectFromCache('\Skif\Comments\Comment', $this->id);
        \Skif\Factory::removeObjectFromCache('\Skif\Comments\Comment', $this->parent_id);

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