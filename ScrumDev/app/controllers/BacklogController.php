<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class BacklogController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {   
        $numberPage = $this->request->getQuery("page", "int");
        $us = Backlog::query()->where("Backlog.id_project = :idproj:")
            ->bind(array("idproj" => $this->session->get("id_proj")))
            ->join("UserStory")
            ->execute();
        
        $paginator = new Paginator(array(
            "data" => $us,
            "limit"=> 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();

    }

    /**
     * Creates a new backlog
	 * 
	 * @param integer $id_project
	 * @param integer $id_us
     */
    public function createAction($id_project, $id_us)
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }

        $backlog = new Backlog();

        $backlog->id_project = $id_project;
        $backlog->id_us = $id_us;

		if(!$backlog->save()){
			foreach ($backlog->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "new"
            ));
		}
		
        $this->flash->success("Backlog associé");

        return $this->dispatcher->forward(array(
            "controller" => "backlog",
            "action" => "index"
        ));

    }

    /**
     * Deletes a backlog
     *
	 * @param integer $id_us
     */
	 
    public function deleteAction($id_us)
    {

        $backlog = Backlog::findFirst(array('id_project = ?0 and id_us = ?1', 'bind' => array(1, $id_us)));
		
        if (!$backlog) {
            $this->flash->error("backlog was not found");

            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }

        if (!$backlog->delete()) {
			
            foreach ($backlog->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }
		
        $this->flash->success("backlog was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "userstory",
            "action" => "delete",
			"params" => array($id_us)
        ));
    }
}
