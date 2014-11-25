<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Backlog extends \Phalcon\Mvc\Model
{
	/**
     * @var integer
     *
     */
    public $id_backlog;

    /**
     * @var integer
     *
     */
    public $id_project;

    /**
     * @var integer
     *
     */
    public $id_us;

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->hasMany("id_project", "project", "id_project");
        $this->hasMany("id_us", "userstory", "id_us");
    }


    /**
     * Test during the creation.
     */
    public function validation()
    {
        if ($this->validationHasFailed() == true) {
            return false;
        }
        
    }


}
