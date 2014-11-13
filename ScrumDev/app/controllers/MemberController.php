<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class MemberController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
	 
		$numberPage=1;
	 
        $member=Member::query()->where("Member.id_project = :idpro:")
		->bind(array("idpro" => $this->session->get("id_proj")))
		->join('Project')
		
		->execute();
		

        $paginator = new Paginator(array(
            "data" => $member,
            "limit"=> 10,
            "page" => $numberPage));

			 $this->view->page = $paginator->getPaginate();
    }


}
?>
