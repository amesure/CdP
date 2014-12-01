<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Userstory extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    public $id_us;

    /**
     * @var integer
     *
     */
    public $id_project;

     /**
     * @var integer
     *
     */
    public $id_sprint;

    /**
     * @var integer
     *
     */
    public $number;

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
        $this->hasMany("id_us", "taskUs", "id_us");
        $this->belongsTo("id_project", "project", "id_project");
    }


    /**
     * Test during the creation if the title is unique.
     */
    public function validation()
    {
        // Test if fields have a value different of nul and empty
        // string.
        $this->validate(new PresenceOf([
          'field' => 'number',
          'message' => 'Un titre est nécessaire.'
        ]));

        $this->validate(new PresenceOf([
          'field' => 'content',
          'message' => 'Une description est nécessaire.'
        ]));

        $this->validate(new PresenceOf([
          'field' => 'cost',
          'message' => 'Un coût est nécessaire.'
        ]));

        if ($this->validationHasFailed() == true) {
            return false;
        }
        
    }
}
