<?php

class UsArchive extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id_us_archive;

    /**
     *
     * @var integer
     */
    public $id_us;

    /**
     *
     * @var integer
     */
    public $id_project;

    /**
     *
     * @var integer
     */
    public $id_sprint;

    /**
     *
     * @var integer
     */
    public $number;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $cost;

    /**
     *
     * @var string
     */
    public $status;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id_us_archive' => 'id_us_archive', 
            'id_us' => 'id_us', 
            'id_project' => 'id_project', 
            'id_sprint' => 'id_sprint', 
            'number' => 'number', 
            'content' => 'content', 
            'cost' => 'cost', 
            'status' => 'status'
        );
    }

}
