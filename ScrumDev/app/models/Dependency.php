<?php
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Dependency extends \Phalcon\Mvc\Model
{
    /**
     * @var integer
     */
    public $id_dependancy;

    /**
     * @var integer
     */
    public $id_task1;

    /**
     * @var integer
     */
    public $id_task2;

     /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->belongsTo("id_task1", "task", "id_task");
        $this->belongsTo("id_task2", "task", "id_task");
    }
}
