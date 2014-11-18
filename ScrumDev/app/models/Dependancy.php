<?php
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOf;

class Dependancy extends \Phalcon\Mvc\Model
{
	/**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $id_task1;

	/**
	 * @var integer
	 */
	public $id_task2;
}
