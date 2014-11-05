<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class UserStory extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    public $id;

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
    public $cost;


    /**
     * Initializer method for model.
     */
    public function initialize()
    {

    }


    /**
     * Test during the creation if the title is unique.
     */
    public function validation()
    {

        // Test if fields have a value different of nul and empty
        // string.
        $this->validate(new PresenceOf([
          'field' => 'title',
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


        // Test if the title doesn't already exist.
        $this->validate(new Uniqueness([
            'field' => 'title',
            'message' => 'Ce nom d\'US est déjà utilisée.'
        ]));

        if ($this->validationHasFailed() == true) {
            return false;
        }
        
    }


}
