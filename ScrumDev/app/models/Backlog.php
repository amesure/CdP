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
    public $id;

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
