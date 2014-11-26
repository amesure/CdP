<?php

class TaskUs extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id_task_us;

    /**
     *
     * @var integer
     */
    public $id_task;

    /**
     *
     * @var integer
     */
    public $id_us;

    public function initialize()
    {
        $this->belongsTo("id_task", "task", "id_task");
        $this->belongsTo("id_us", "userstory", "id_us");
    }

}
