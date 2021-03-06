<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Project extends \Phalcon\Mvc\Model
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
    public $access;


    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->hasMany("id_project", "member", "id_project");
        $this->hasMany("id_project", "sprint", "id_project");
        $this->hasMany("id_project", "userstory", "id_project");

    }


    /**
     * Tests
     */
    public function validation()
    {

        // Test if fields have a value different of nul and empty
        // string.
        $this->validate(new PresenceOf([
          'field' => 'title',
          'message' => 'Un titre est nécessaire.'
        ]));

        $this->validate(new Uniqueness([
            'field' => 'title',
            'message' => 'Ce titre est déjà utilisé.'
        ]));

        if ($this->validationHasFailed() == true) {
            return false;
        }
        
    }
}
