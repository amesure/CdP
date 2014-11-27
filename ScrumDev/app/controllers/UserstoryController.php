<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserstoryController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $id_project = $this->session->get('id_proj');
        $backlog = Userstory::findByid_project($id_project);
        if (!$backlog) {
            $this->flash->error("us was not found");

            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }
        $this->view->page = $backlog;
    }

    /**
     * Edits a us
     *
     * @param string $id_us
     */
    public function editAction($id_us)
    {
        if (!$this->request->isPost()) {
            $us = Userstory::findFirstByid_us($id_us);
            if (!$us) {
                $this->flash->error("us was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "userstory",
                    "action" => "index"
                ));
            }

            $this->view->setVar("id", $us->id_us);
            $this->tag->setDefault("id", $us->id_us);
            $this->tag->setDefault("title", $us->title);
            $this->tag->setDefault("content", $us->content);
            $this->tag->setDefault("cost", $us->cost);
        }
    }


    public function newAction()
    {

    }

    /**
     * Creates a new us
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }

        $us = new Userstory();
        $us->id_project = $this->session->get('id_proj');
        $us->id_sprint = 1;
        $us->number = $this->request->getPost("number");
        $us->content = $this->request->getPost("content");
        $us->cost = $this->request->getPost("cost");

        if (!$us->save()) {
            foreach ($us->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "new"
            ));
        }

        $this->flash->success("US créée");

        return $this->dispatcher->forward(array(
            "controller" => "userstory",
            "action" => "index",
        ));

    }

    /**
     * Saves a us edited
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }

        $id_us = $this->request->getPost("id");

        $us = Userstory::findFirst(array(
            'id_us = :id_us:',
            'bind' => array('id_us' => $id_us)
        ));
        if (!$us) {
            $this->flash->error("Cette US n'existe pas " . $id_us);

            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }

        $us->id_us = $id_us;
        $us->title = $this->request->getPost("title");
        $us->content = $this->request->getPost("content");
        $us->cost = $this->request->getPost("cost");
        
        if (!$us->save()) {
            foreach ($us->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "edit",
                "params" => array($us->id_us)
            ));
        }

        $this->flash->success("US was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "userstory",
            "action" => "index"
        ));

    }

    /**
     * Deletes a us
     *
     * @param string $id_us
     */
    public function deleteAction($id_us)
    {

        $us = Userstory::findFirstByid_us($id_us);
        if (!$us) {
            $this->flash->error("us was not found");

            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }

        if (!$us->delete()) {
            foreach ($us->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "userstory",
                "action" => "index"
            ));
        }

        $this->flash->success("us was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "userstory",
            "action" => "index"
        ));
    }
}
