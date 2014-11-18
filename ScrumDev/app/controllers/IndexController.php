<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
    	$this->session->remove('id_proj');
    }

}

