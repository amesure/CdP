<?php
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Task extends \Phalcon\Mvc\Model
{
    /**
     * @var integer
     */
    public $id_task;

    /**
     * @var integer
     */
    public $id_user;

    /**
    * @var integer
    */
    public $id_sprint;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var integer
     */
    public $cost;

    /**
     * @var string
     */
    public $status;

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->hasMany("id_task", "dependancy", "id_task1");
        $this->hasMany("id_task", "dependancy", "id_task2");
        $this->hasMany("id_task", "taskus", "id_task");
        $this->belongsTo("id_user", "user", "id_user");
        $this->belongsTo("id_sprint", "sprint", "id_sprint");

    }

    /**
     * Test during the creation.
     */
    public function validation()
    {
        $this->validate(new PresenceOf([
          'field' => 'title',
          'message' => 'Un titre est necessaire.'
        ]));

        $this->validate(new PresenceOf([
          'field' => 'content',
          'message' => 'Un contenu est necessaire.'
        ]));

        $this->validate(new PresenceOf([
          'field' => 'id_sprint',
          'message' => 'Un sprint est necessaire.'
        ]));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
