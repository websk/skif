<?php
namespace Skif\Task;

class Task implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $created_time;
    protected $description_task;
    protected $comment_in_task;
    protected $assigned_to_user_id;
    protected $resolved_time;
    protected $status;
    protected $created_user_id;

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
    public function getCreatedTime()
    {
        return $this->created_time;
    }

    /**
     * @param mixed $created_time
     */
    public function setCreatedTime($created_time)
    {
        $this->created_time = $created_time;
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
    public function getResolvedTime()
    {
        return $this->resolved_time;
    }

    /**
     * @param mixed $resolved_time
     */
    public function setResolvedTime($resolved_time)
    {
        $this->resolved_time = $resolved_time;
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

}