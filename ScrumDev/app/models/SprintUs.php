<?php

class SprintUs extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id_sprint_us;

    /**
     *
     * @var integer
     */
    public $id_sprint;

    /**
     *
     * @var integer
     */
    public $id_us;

    public function initialize()
    {
        $this->belongsTo("id_sprint", "Sprint", "id_sprint");
        $this->belongsTo("id_us", "UserStory", "id_us");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id_sprint_us' => 'id_sprint_us', 
            'id_sprint' => 'id_sprint', 
            'id_us' => 'id_us'
        );
    }

}
