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
		$task->status = 0;
        $task->id_sprint = $this->session->get("id_sprint");

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
			$this->tag->setDefault("status", $task->status);
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
		$task->status = $this->request->getPost("status");
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
        $i = 0;
		foreach($task as $t){
			$td[$i] = new Taskdep();
			$td[$i]->id_task = $t->id_task;
            $td[$i]->title = $t->title;
            $td[$i]->content = $t->content;
            $td[$i]->cost = $t->cost;
			$deps = Dependency::query()
				->where("id_task1 = :id_task:")
				->andWhere("id_task2 = :id_t2:")
				->bind(array("id_task"=>$id_task, "id_t2"=>$td[$i]->id_task))
				->execute();
			if($deps->count() == 1){
				$td[$i]->dep = true;
			}
			else{
				$td[$i]->dep = false;
			}
			$i += 1;
		}
        $this->view->page = $td;
        $this->view->id_task = $id_task;
        $this->tag->setDefault("id_task", $id_task);
    }

    /**
     * New dependancies
     */
    public function newdependancyAction()
    {
        $dependancies = $this->request->getPost("dependancy");
        $id_task = $this->request->getPost("id_task");
        /* On récupère les anciennes dépendances qu'on transforme en array */
		$old_deps = Dependency::query()
			->where("id_task1 = :id_task:")
			->bind(array("id_task"=>$id_task))
			->execute();
		$old_deps_array = $old_deps->toArray();
		if($dependancies){
			/* On ajoute celles qui sont à ajouter en évitant les doublons et double sens */
			foreach ($dependancies as $id_dep) {
				$deps = Dependency::query()
					->where("id_task1 = :id_task: AND id_task2 = :id_dep:")
					->bind(array("id_task"=>$id_dep, "id_dep"=>$id_task))
					->execute();
				/* Vérification non double sens */
				if ($deps->count() >= 1) {
					echo "Double sens";
					return $this->dispatcher->forward(array(
						"controller" => "task",
						"action" => "dependancy",
						"params" => array($id_task)
					));
				} else {
					$deps2 = Dependency::query()
						->where("id_task1 = :id_dep: AND id_task2 = :id_task:")
						->bind(array("id_task"=>$id_dep, "id_dep"=>$id_task))
						->execute();
					/* Vérification pour éviter les doublons */
					if($deps2->count() < 1){
						$dep = new Dependency();
						$dep->id_task1 = $id_task;
						$dep->id_task2 = $id_dep;
						if (!$dep->save()) {
							foreach ($dep->getMessages() as $message) {
								$this->flash->error($message);
							}
						}
					} else {
						echo "La dépendance de la tache " . strval($id_task) . " vers la tache " . strval($id_dep) . " existe déjà";
					}
				}
				/* On supprime de l'array celles qui sont déjà présentent dans les anciennes dépandences */
				if(count($old_deps_array) > 0){
					for($i=0; $i<count($old_deps_array); $i++){
						if($old_deps_array[$i]["id_task2"] == $id_dep){
							unset($old_deps_array[$i]);
						}
					}
				}
				/* On remet l'array avec en premier indice 0 */
				$old_deps_array = array_values($old_deps_array);
            }
        }
		/* On regarde les dépendances qui restent et on les supprime */
		if(count($old_deps_array) > 0){
			for($i=0; $i<count($old_deps_array); $i++){
				$dep = Dependency::query()
					->where("id_dependancy = :id_dep:")
					->bind(array("id_dep"=>$old_deps_array[$i]["id_dependancy"]))
					->execute();
				$dep->delete();
			}
		}
        return $this->dispatcher->forward(array(
            "controller" => "task",
            "action" => "index"
        ));
    }
	
	/**
     * Shows a form to select the US that this task is related to
     *
     * @param string $id_task
     */
    public function taskUsAction($id_task)
    {
        $us = Userstory::query()
            ->where("id_sprint = :id_sprint:")
            ->bind(array("id_sprint"=>$this->session->get("id_sprint")))
            ->execute();
		$i = 0;
		foreach($us as $u){
			$tu[$i] = new TaskUsCheck();
			$tu[$i]->id_us = $u->id_us;
			$tu[$i]->id_project = $u->id_project;
            $tu[$i]->id_sprint = $u->id_sprint;
            $tu[$i]->number = $u->number;
            $tu[$i]->content = $u->content;
            $tu[$i]->cost = $u->cost;
			$deps = TaskUs::query()
				->where("id_task = :id_task:")
				->andWhere("id_us = :id_us:")
				->bind(array("id_task"=>$id_task, "id_us"=>$tu[$i]->id_us))
				->execute();
			if($deps->count() == 1){
				$tu[$i]->dep = true;
			}
			else{
				$tu[$i]->dep = false;
			}
			$i += 1;
		}
        $this->view->page = $tu;
        $this->view->id_task = $id_task;
        $this->tag->setDefault("id_task", $id_task);
    }

    /**
     * New relation between task and US
     */
    public function newTaskUSAction()
    {
        $dependancies = $this->request->getPost("dependancy");
        $id_task = $this->request->getPost("id_task");
        /* On récupère les anciennes dépendances qu'on transforme en array */
		$old_deps = TaskUs::query()
			->where("id_task = :id_task:")
			->bind(array("id_task"=>$id_task))
			->execute();
		$old_deps_array = $old_deps->toArray();
		if($dependancies){
			/* On ajoute celles qui sont à ajouter en évitant les doublons et double sens */
			foreach ($dependancies as $id_us) {
				$deps = TaskUs::query()
					->where("id_task = :id_task: AND id_us = :id_us:")
					->bind(array("id_task"=>$id_task, "id_us"=>$id_us))
					->execute();
				/* Vérification pour éviter les doublons */
				if($deps->count() < 1){
					$dep = new TaskUs();
					$dep->id_task = $id_task;
					$dep->id_us = $id_us;
					if (!$dep->save()) {
						foreach ($dep->getMessages() as $message) {
							$this->flash->error($message);
						}
					}
				} else {
					echo "Le lien entre la tache " . strval($id_task) . " et l'US " . strval($id_us) . " existe déjà";
				}
				/* On supprime de l'array celles qui sont déjà présentent dans les anciennes dépandences */
				if(count($old_deps_array) > 0){
					for($i=0; $i<count($old_deps_array); $i++){
						if($old_deps_array[$i]["id_us"] == $id_us){
							unset($old_deps_array[$i]);
						}
					}
				}
				/* On remet l'array avec en premier indice 0 */
				$old_deps_array = array_values($old_deps_array);
            }
        }
		/* On regarde les dépendances qui restent et on les supprime */
		if(count($old_deps_array) > 0){
			for($i=0; $i<count($old_deps_array); $i++){
				$dep = TaskUs::query()
					->where("id_task_us = :id_task_us:")
					->bind(array("id_task_us"=>$old_deps_array[$i]["id_task_us"]))
					->execute();
				$dep->delete();
			}
		}
        return $this->dispatcher->forward(array(
            "controller" => "task",
            "action" => "index"
        ));
    }
}
