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
    public $id;

    /**
     * @var string
     *
     */
    public $login;

    /**
     * @var string
     *
     */
    public $password;

    /**
     * @var email
     *
     */
    public $email;


    /**
     * Initializer method for model.
     */
    public function initialize()
    {
		$this->hasMany("id", "member", "id_user");
    }


    /**
     * Test during the creation if the login and the email address 
     * is unique.
     */
    public function validation()
    {

        // Test if fields have a value different of nul and empty
        // string.
        $this->validate(new PresenceOf([
          'field' => 'login',
          'message' => 'Un identifiant est nécessaire.'
        ]));

        $this->validate(new PresenceOf([
          'field' => 'email',
          'message' => 'Une adresse mail est nécessaire.'
        ]));

        $this->validate(new PresenceOf([
          'field' => 'password',
          'message' => 'Un mot de passe est nécessaire.'
        ]));


        // Test if the login or the email doesn't already exist.
        $this->validate(new Uniqueness([
            'field' => 'email',
            'message' => 'Cette adresse est déjà utilisée.'
        ]));

        $this->validate(new Uniqueness([
            'field' => 'login',
            'message' => 'Cet identifiant existe déjà.'
        ]));


        // Allows to validate if email field has correct value
        $this->validate(new EmailValidator([
            'field' => 'email'
        ]));


        if ($this->validationHasFailed() == true) {
            return false;
        }
        
    }


}
