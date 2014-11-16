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
	
	public function fireAction($idmember)
	{
		$member= Member::findFirstByid_member($idmember);
		if (!$member->delete()) {

            foreach ($project->getMessages() as $message) {
                $this->flash->error($message);
            }           
        }
		$this->flash->success("le membre a été retiré avec succés");
		return $this->dispatcher->forward(array(
                "controller" => "member",
                "action" => "index"
            ));
	}

	public function acceptAction($idmember)
    {
		
		$member= Member::findFirstByid_member($idmember);
		$member->type=1;
		if (!$member->save()) {

            foreach ($project->getMessages() as $message) {
                $this->flash->error($message);
            }           
        }
		
		$this->flash->success("le membre a été ajouté avec succés");
		return $this->dispatcher->forward(array(
                "controller" => "member",
                "action" => "index"
            ));

        }
		
		public function invitAction($id_user)
		{
			$member=new Member();
			$member->id_user=$id_user;
			$member->id_project=$this->session->get("idproj");
			$member->type=2;
			if (!$member->save()) {
            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "member",
                "action" => "index"
            )); }
			
			$this->flash->success("L'invitaion a été envoyé");
			return $this->dispatcher->forward(array(
                "controller" => "member",
                "action" => "index"));
		}
			

}
?>
