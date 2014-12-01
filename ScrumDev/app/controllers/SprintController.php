<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class SprintController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->updateStatus();
        if ($this->session->has("id_proj")) {
            $id_proj = $this->session->get("id_proj");
        } else {
            $this->flash->error("Vous n'avez pas accès à cette page.");
            
            return $this->dispatcher->forward(array(
                "controller" => "index",
                "action" => "index"
            ));
        }
        $sprints = Sprint::find(array(
            'id_project = '.$id_proj.''
        ));
        
        $this->session->remove("id_sprint");
        $this->view->setVar('sprint', $sprints);
    }


    /**
     * Displayes the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a sprint
     *
     * @param string $id_sprint
     */
    public function editAction($id_sprint)
    {
        if (!$this->request->isPost()) {
            $sprint = Sprint::findFirstByid_sprint($id_sprint);
            if (!$sprint) {
                $this->flash->error("sprint was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "sprint",
                    "action" => "index"
                ));
            }
            $this->tag->setDefault('idsprint',$id_sprint);
            $this->tag->setDefault("number", $sprint->number);
            $this->tag->setDefault("begin", $sprint->begin);
            $this->tag->setDefault("end", $sprint->end);
        }
    }

    /**
     * Creates a new sprint
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "index"
            ));
        }

        $sprint = new Sprint();

        $sprint->number = $this->request->getPost("number");
        $sprint->id_project = $this->session->id_proj;
        $sprint->begin = $this->request->getPost("begin");
        $sprint->end = $this->request->getPost("end");
        $sprint->status = "todo";
        

        if (!$sprint->save()) {
            foreach ($sprint->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "new"
            ));
        }

        $this->flash->success("sprint was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "index"
        ));

    }

    /**
     * Saves a sprint edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "index"
            ));
        }
        $id_sprint = $this->request->getPost("idsprint");
        $sprint = Sprint::findFirstByid_sprint($id_sprint);
        if (!$sprint) {
            $this->flash->error("sprint does not exist " . $id_sprint);

            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "index"
            ));
        }

        $sprint->number = $this->request->getPost("number");
        $sprint->id_project = $this->session->id_proj;
        $sprint->begin = $this->request->getPost("begin");
        $sprint->end = $this->request->getPost("end");
        

        if (!$sprint->save()) {
            foreach ($sprint->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "edit",
                "params" => array($sprint->id_sprint)
            ));
        }

        $this->flash->success("sprint was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "index"
        ));

    }

    /**
     * Deletes a sprint
     *
     * @param string $id_sprint
     */
    public function deleteAction($id_sprint)
    {

        $sprint = Sprint::findFirstByid_sprint($id_sprint);
        if (!$sprint) {
            $this->flash->error("sprint was not found");

            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "index"
            ));
        }

        if (!$sprint->delete()) {
            foreach ($sprint->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "sprint",
                "action" => "search"
            ));
        }

        $this->flash->success("sprint was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "index"
        ));
    }


    public function showAction($id_sprint)
    {   
        $sprint = Sprint::findFirstByid_sprint($id_sprint);
		$us = Userstory::query()
			->where("id_sprint = :id_sprint:")
			->bind(array("id_sprint"=>$id_sprint))
			->execute();
		$status[0] = false;
		foreach($us as $u){
			$i = 0;
			$tasks = Task::query()
				->join('TaskUs')
				->where("TaskUs.id_task = Task.id_task AND TaskUs.id_us = :id_us:")
				->bind(array("id_us"=>$u->id_us))
				->execute();
			$usdone = true;
			foreach($tasks as $task){
				if($task->status == 0 || $task->status == 1)
					$usdone = false;
			}
			$status[$i] = $usdone;
			$i++;
		}
        if ($sprint === false) {
            $this->flash->error("Ce sprint na pas été trouvé.");
            $this->dispatcher->forward(array(
                'controller' => 'sprint',
                'action' => 'index'
            ));
        }
        $this->session->set("id_sprint", $id_sprint);
        
        $prog = "";
        $date = date("Y-m-d");
        if ($date < $sprint->begin) {
            $prog = "Débutera le ".$sprint->begin;
        } elseif ($date < $sprint->end) {
            $prog = "En cours";
        } else {
            $prog = "Fini";
        }

        $uss = Userstory::findByid_sprint($sprint->id_sprint);
        if ($sprint->status === "closed"){
            foreach ($uss as $us) {
                $us_arch = new UsArchive();
                $us_arch->id_us = $us->id_us;
                $us_arch->id_project = $us->id_project;
                $us_arch->id_sprint = $us->id_sprint;
                $us_arch->number = $us->number;
                $us_arch->content = $us->content;
                $us_arch->cost = $us->cost;
                $us_arch->status = $us->status;
                if (!$us_arch->save()) {
                    foreach ($us_arch->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
            }
            $sprint->status = "archived";
            if (!$sprint->save()) {
                foreach ($sprint->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }
            $us = UsArchive::findByid_sprint($sprint->id_sprint);
        }
        if ($sprint->status === "archived" || $sprint->status === "closed") {
            $us = UsArchive::findByid_sprint($sprint->id_sprint);
        }

        $this->view->setVar('sprint', $sprint);
		$this->view->setVar('us', $us);
		$this->view->setVar('status', $status);
        $this->view->setVar('prog', $prog);
    }


    public function assignAction($id_sprint)
    {
        $userstories = Userstory::findByid_project($this->session->get("id_proj"));
        if ($userstories->count()==0){
            $this->flash->error("Ce projet de comprend aucune userstory, veuillez
                créer au moins une userstory et un sprint.");
            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }
        $this->view->setVar("userstories", $userstories);
        $this->view->setVar("id_sprint", $id_sprint);
        
    }

    public function saveAssignAction()
    {
        // We set all the US from the current sprint to null.
        $old_uss = Userstory::findByid_sprint($this->session->get("id_sprint"));
        foreach ($old_uss as $old_us) {
            $old_us->id_sprint = null;
            if (!$old_us->save()) {
                $this->flash->error("La modification sur l'US".$old_us->number
                    ." (id:".$old_us->id_us.") n'a pû être sauvegardé.");
                return $this->dispatcher->forward(array(
                    "controller" => "sprint",
                    "action" => "assign",
                    "params" => array($this->session->get("id_sprint"))
                ));
            }
        }
        // We set for each userstory checked the field id_sprint to the current
        // id_sprint
        $assignTab = $this->request->getPost("assign");
        foreach ($assignTab as $assign) {
            $userstory = Userstory::findFirst($assign);
            $userstory->id_sprint = $this->session->get("id_sprint");

            if (!$userstory->save()) {
                $this->flash->error("La modification sur l'US".$userstory->number
                    ." (id:".$userstory->id_us.") n'a pû être sauvegardé.");
                return $this->dispatcher->forward(array(
                    "controller" => "sprint",
                    "action" => "assign",
                    "params" => array($this->session->get("id_sprint"))
                ));
            }
        }
        $this->flash->success("sprint was updated successfully");


        return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "assign",
            "params" => array($this->session->get("id_sprint"))
        ));
        
    }

    private function updateStatus(){
        $sprints = Sprint::findByid_project($this->session->get('id_proj'));
        $date = date("Y-m-d");
        foreach ($sprints as $sprint) {
            if ($date < $sprint->begin) {
                $sprint->status = "todo";
            } elseif ($date < $sprint->end) {
                $sprint->status = "ongoing";
            } elseif ($sprint->status !== "archived") {
               $sprint->status = "closed";
            }
            if (!$sprint->save()) {
                foreach ($sprint->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }
        }
    }


	
	public function kanbanAction()
	{
		$todo= Task::query()
            ->where("id_sprint = :sprint:")
            ->andWhere("status='to do'")
            ->bind(array("sprint" => $this->session->get("id_sprint")))
            ->execute();
		
		$inprogress=Task::query()
            ->where("id_sprint = :sprint:")
            ->andWhere("status='in progress'")
            ->bind(array("sprint" => $this->session->get("id_sprint")))
            ->execute();
		
		$done=Task::query()
            ->where("id_sprint = :sprint:")
            ->andWhere("status='done'")
            ->bind(array("sprint" => $this->session->get("id_sprint")))
            ->execute();
		
		$this->view->todo=$todo;
		$this->view->inprogress=$inprogress;
		$this->view->done=$done;
	}
}
