<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class TaskController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        if (!$this->session->has('auth')) {
            return $this->dispatcher->forward(array(
                "controller" => "",
                "action" => ""
            ));
        } else {
            if ($this->session->has("id_sprint")) {
                $task = Task::query()
                    ->where("id_sprint = :id_sprint:")
                    ->bind(array(
                        "id_sprint" => $this->session->get("id_sprint")))
                    ->execute();

                $this->view->page = $task;
            } else {
                return $this->dispatcher->forward(array(
                    "controller" => "sprint",
                    "action" => "index"
                ));
            }
        }
    }

    /**
     * Displays form
     */
    public function newAction()
    {

    }

    /**
     * Creates a task
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "index"
            ));
        }
        $task = new Task();
        $task->title = $this->request->getPost("title");
        $task->content = $this->request->getPost("content");
        $task->cost = $this->request->getPost("cost");
        $task->id_sprint = $this->session->get("id_sprint");
		$task->status="to do";

        if (!$task->save()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "new"
            ));
        }
        $this->flash->success("Tache créée");

        return $this->dispatcher->forward(array(
            "controller" => "task",
            "action" => "index"
        ));
    }

    /**
     * Edits a task
     *
     * @param string $id_task
     */
    public function editAction($id_task)
    {
        if (!$this->request->isPost()) {
            $task = Task::findFirstByid_task($id_task);
            if (!$task) {
                $this->flash->error("task was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "task",
                    "action" => "index"
                ));
            }

            $this->view->id_task = $task->id_task;

            $this->tag->setDefault("id_task", $task->id_task);
            $this->tag->setDefault("title", $task->title);
            $this->tag->setDefault("content", $task->content);
            $this->tag->setDefault("cost", $task->cost);
        }
    }

    /**
     * Saves a task
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "index"
            ));
        }
        $id_task = $this->request->getPost("id_task");
        $task = Task::findFirstByid_task($id_task);
        if (!$task) {
            $this->flash->error("Cette tache n'existe pas " . $id_task);
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "index"
            ));
        }
        $task->title = $this->request->getPost("title");
        $task->content = $this->request->getPost("content");
        $task->cost = $this->request->getPost("cost");
        if (!$task->save()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "edit",
                "params" => array($task->id_task)
            ));
        }
        $this->flash->success("task was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "task",
            "action" => "index"
        ));
    }

    /**
     * Deletes a task
     *
     * @param string $id_task
     */
    public function deleteAction($id_task)
    {
        $task = Task::findFirstByid_task($id_task);
        if (!$task) {
            $this->flash->error("Cette tache n'existe pas " . $id_task);
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "index"
            ));
        }
        $deps = Dependancy::query()
            ->where("id_task1 = :id_task: OR id_task2 = :id_task:")
            ->bind(array("id_task"=>$id_task))
            ->execute();
        if ($deps) {
            foreach ($deps as $dep) {
                $dep->delete();
            }
        }
        if (!$task->delete()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "task",
                "action" => "index"
            ));
        }
        $this->flash->success("task was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "task",
            "action" => "index"
        ));
    }

    /**
     * Shows a form to select the depandancies
     *
     * @param string $id_task
     */
    public function dependancyAction($id_task)
    {
        $task = Task::query()
            ->where("id_sprint = :id_sprint:")
            ->andWhere("id_task <> :id_task:")
            ->bind(array("id_sprint"=>$this->session->get("id_sprint"), "id_task"=>$id_task))
            ->execute();
        $this->view->page = $task;
        $this->view->id_task = $id_task;
        $this->tag->setDefault("id_task", $id_task);

        $deps = Dependancy::query()->where("id_task1 = :id_task:")->bind(array("id_task"=>$id_task))->execute();
        if ($deps) {
            foreach ($deps as $dep) {
                $dep->delete();
            }
        }
    }

    /**
     * New the dependancies
     */
    public function newdependancyAction()
    {
        $dependancies = $this->request->getPost("dependancy");
        $id_task = $this->request->getPost("id_task");
        foreach ($dependancies as $id_dep) {
            echo $id_task;
            echo $id_dep;
            if ($id_dep) {
                $bool = false;
                $deps = Dependancy::query()
                    ->where("id_task1 = :id_task: AND id_task2 = :id_dep:")
                    ->bind(array("id_task"=>$id_dep, "id_dep"=>$id_task))
                    ->execute();
                foreach ($deps as $dep) {
                    if (!$bool && $dep) {
                        $bool = true;
                    }
                }
                if ($bool) {
                    return $this->dispatcher->forward(array(
                        "controller" => "task",
                        "action" => "dependancy",
                        "params" => array($id_task)
                    ));
                } else {
                    $dep = new Dependancy();
                    $dep->id_task1 = $id_task;
                    $dep->id_task2 = $id_dep;
                    if (!$dep->save()) {
                        foreach ($dep->getMessages() as $message) {
                            $this->flash->error($message);
                        }
                    }
                }
            }
        }
        return $this->dispatcher->forward(array(
            "controller" => "task",
            "action" => "index"
        ));
    }
	
	public function todoAction($idtask)
	{
		 $task = Task::findFirstByid_task($idtask);
		 if($task->id_user==NULL or $task->id_user==$this->session->get('auth')){
			$task->id_user=NULL;
			$task->status="to do";
			 if (!$task->save()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);}
				}
			$this->session->set('canceltask',$task->id_task);
			}
		
		else{
			$this->flash->error("Vous n'êtes pas le responsable de la tâche");
			}
			
		return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "kanban"
        ));
		
	}
	
	public function inprogressAction($idtask)
	{
		 $task = Task::findFirstByid_task($idtask);
		 if($task->id_user==NULL or $task->id_user==$this->session->get('auth')){
			$task->id_user=$this->session->get('auth');
			$task->status="in progress";
			 if (!$task->save()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);}
				}
			if($this->session->get('canceltask')!=$task->id_task){
			$this->session->set('canceltask',$task->id_task);}
			else{
			$this->session->remove('canceltask');
			}
			}
		
		else{
			$this->flash->error("Vous n'êtes pas le responsable de la tâche");
			}
			
		return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "kanban"
        ));
		
	}
	
		public function doneAction($idtask)
	{
		 $task = Task::findFirstByid_task($idtask);
		 if($task->id_user==NULL or $task->id_user==$this->session->get('auth')){			
			$task->status="done";
			 if (!$task->save()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);}
				}
			$this->session->set('canceltask',$task->id_task);
			}
		
		else{
			$this->flash->error("Vous n'êtes pas le responsable de la tâche");
			}
			
		return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "kanban"
        ));
		
	}
	
	public function switchAction($idtask)
	{
		if($this->request->isPost()){
		 $iduser = $this->request->getPost("User");
		 $task = Task::findFirstByid_task($idtask);
		 $task->id_user=$iduser;
		 if (!$task->save()) {
            foreach ($task->getMessages() as $message) {
                $this->flash->error($message);}
			}		 
		 }
		return $this->dispatcher->forward(array(
            "controller" => "sprint",
            "action" => "kanban"
        ));
	}
			
		
	
	
}
