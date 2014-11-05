<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Task extends \Phalcon\Mvc\Model
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
    public $id_member;

    /**
     * @var integer
     *
     */
    public $title;

	/**
     * @var string
     *
     */
    public $content;

	/**
     * @var integer
     *
     */
    public $cost;

	/**
     * @var string
     *
     */
    public $status;

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
		$this->validate(new PresenceOf([
          'field' => 'title',
          'message' => 'Un titre est nécessaire.'
        ]));
		
		$this->validate(new PresenceOf([
          'field' => 'content',
          'message' => 'Un contenu est nécessaire.'
        ]));
	
        if ($this->validationHasFailed() == true) {
            return false;
        }
        
    }


}
