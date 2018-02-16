<?php

namespace Skif\Poll;

use Skif\Model\FactoryTrait;
use Skif\Model\InterfaceDelete;
use Skif\Model\InterfaceFactory;
use Skif\Model\InterfaceGetTitle;
use Skif\Model\InterfaceLoad;
use Skif\Model\InterfaceSave;
use Skif\Util\ActiveRecord;

class PollQuestion implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceGetTitle
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'poll_question';

    protected $id;
    protected $title = '';
    protected $poll_id;
    protected $votes = 0;
    protected $weight = 0;


    public static $crud_create_button_required_fields_arr = array('poll_id');
    public static $crud_create_button_title = 'Добавить вариант ответа';

    public static $crud_model_class_screen_name = 'Заголовок';
    public static $crud_model_title_field = 'title';

    public static $crud_field_titles_arr = array(
        'title' => 'Заголовок',
        'poll_id' => 'Опрос',
        'votes' => 'Проголосовало',
        'weight' => 'Сортировка'
    );

    public static $crud_model_class_screen_name_for_list = 'Варианты ответов';

    public static $crud_fast_create_field_name = 'title';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'title' => array('col_class' => 'col-md-4 col-sm-4 col-xs-4'),
        'poll_id' => array('col_class' => 'col-md-2 col-sm-2 col-xs-2'),
        'votes' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'title' => array(),
        'poll_id' => array(
            'widget' => array('\Skif\CRUD\ModelReferenceWidget\ModelReferenceWidget', 'renderWidget'),
            'widget_settings' => array(
                'model_class_name' => '\Skif\Poll\Poll'
            )
        ),
        'votes' => array(),
        'weight' => array(),
    );


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
    public function getPollId()
    {
        return $this->poll_id;
    }

    /**
     * @param mixed $poll_id
     */
    public function setPollId($poll_id)
    {
        $this->poll_id = $poll_id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public static function afterUpdate($id)
    {
        $poll_question_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        Poll::afterUpdate($poll_question_obj->getPollId());
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        Poll::afterUpdate($this->getPollId());
    }

}