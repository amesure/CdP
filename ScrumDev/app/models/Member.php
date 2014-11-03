<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class User extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    public $id_project;

    /**
     * @var string
     *
     */
    public $id_user;

    /**
     * @var string
     *
     */

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
		$this->belongsTo("id_project", "project", "id_project");
        $this->belongsTo("id_user", "user", "id");
    }


    /**
     * Test during the creation if the login and the email address 
     * is unique.
     */
    public function validation()
    {
        
    }


}
