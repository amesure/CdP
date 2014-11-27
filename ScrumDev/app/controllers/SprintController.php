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

        $this->view->setVar('sprint', $sprint);
        $this->view->setVar('prog', $prog);
    }
}
