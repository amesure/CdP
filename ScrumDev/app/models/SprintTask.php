<?php

class SprintTask extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id_sprint_task;

    /**
     *
     * @var integer
     */
    public $id_sprint;

    /**
     *
     * @var integer
     */
    public $id_task;

    public function initialize()
    {
        $this->belongsTo("id_sprint", "Sprint", "id_sprint");
        $this->belongsTo("id_task", "Task", "id_task");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id_sprint_task' => 'id_sprint_task', 
            'id_sprint' => 'id_sprint', 
            'id_task' => 'id_task'
        );
    }

}
