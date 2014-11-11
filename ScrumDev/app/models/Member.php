<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Member extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    public $id_project;

    /**
     * @var integer
     *
     */
    public $id_user;

    /**
     * @var integer
     *
     */
	public $id_member;
	
	public $type;
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