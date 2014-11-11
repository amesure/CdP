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

    public function initialize()
    {
        $this->hasMany("id_sprint", "SprintUs", "id_sprint");
        $this->hasMany("id_sprint", "SprintTask", "id_sprint");
    }


}
