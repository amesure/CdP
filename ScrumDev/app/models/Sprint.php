<?php

class Sprint extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id_sprint;

    /**
     *
     * @var integer
     */
    public $number;

    /**
     *
     * @var integer
     */
    public $id_project;

    /**
     *
     * @var string
     */
    public $begin;

    /**
     *
     * @var string
     */
    public $end;

    /**
     *
     * @var string
     */
    public $status;


    public function initialize()
    {
        $this->hasMany("id_sprint", "Task", "id_sprint");
        $this->belongsTo("id_project", "Project", "id_project");
    }
}
