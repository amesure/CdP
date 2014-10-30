<?php


class Users extends \Phalcon\Mvc\Model
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

    }

}
