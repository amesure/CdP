<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserStoryController extends ControllerBase
{

    /**
     * Edits a us
     *
     * @param string $id_us
     */
    public function editAction($id_us)
    {

        if (!$this->request->isPost()) {

            $us = UserStory::findFirstByid_us($id_us);
            if (!$us) {
                $this->flash->error("us was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "backlog",
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

    /**
     * Creates a new us
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }

        $us = new UserStory();

        $us->title = $this->request->getPost("title");
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
            "controller" => "backlog",
            "action" => "create",
			"params" => array($this->session->get('id_proj'), $us->id_us)
        ));

    }

    /**
     * Saves a us edited
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }
		
        $id_us = $this->request->getPost("id");

		$us = UserStory::findFirst(array(
			'id_us = :id_us:',
			'bind' => array('id_us' => $id_us)
		));
        if (!$us) {
            $this->flash->error("Cette US n'existe pas " . $id_us);

            return $this->dispatcher->forward(array(
                "controller" => "backlog",
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
            "controller" => "backlog",
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

        $us = UserStory::findFirstByid_us($id_us);
        if (!$us) {
            $this->flash->error("us was not found");

            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }

        if (!$us->delete()) {

            foreach ($us->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "backlog",
                "action" => "index"
            ));
        }

        $this->flash->success("us was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "backlog",
            "action" => "index"
        ));
    }
}
