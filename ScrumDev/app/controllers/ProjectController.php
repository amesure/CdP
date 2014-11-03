<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class ProjectController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }
	
	/**
	* Show action
	*/
	public function showAction()
	{
	$id_user=$this->session->get("auth");
	$id_project=Member::findFirstByid_user($id_user);
	$project=Project::findFirstByid_project($id_project);
	$this->view->setVars(array(
            'title' => $project->title,
            'content' => $project->content
        ));
	}

    /**
     * Searches for project
     */
    public function searchAction()
    {
  
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Project", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "id_project";

        $Project = Project::find($parameters);
        if (count($project) == 0) {
            $this->flash->notice("The search did not find any project");

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $project,
            "limit"=> 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }


    /**
     * Displayes the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a project
     *
     * @param string $id_project
     */
    public function editAction($id_project)
    {

        if (!$this->request->isPost()) {

            $project = Project::findFirstByid_project($id_project);
            if (!$) {
                $this->flash->error("project was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "project",
                    "action" => "index"
                ));
            }

            $this->view->id_project = $project->id_project;

            $this->tag->setDefault("id_project", $project->id_project);
            $this->tag->setDefault("title", $project->title);
            $this->tag->setDefault("content", $project->content);
            $this->tag->setDefault("access", $project->access);
            
        }
    }

    /**
     * Creates a new project
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $project = new Project();

        $project->title = $this->request->getPost("title");
        $project->content = $this->request->getPost("content");
        $project->access = $this->request->getPost("access");
        

        if (!$project->save()) {
            foreach ($project->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "new"
            ));
        }

        $this->flash->success("Votre inscription s'est déroulée correctement");

        return $this->dispatcher->forward(array(
            "controller" => "project",
            "action" => "index"
        ));

    }

    /**
     * Saves a project edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $id_project = $this->request->getPost("id_project");

        $project = Project::findFirstByid_project($id_project);
        if (!$project) {
            $this->flash->error("Cet utilisateur n'existe pas " . $id_project);

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $project->title = $this->request->getPost("title");
        $project->content = $this->request->getPost("content", "content");
        $project->access = $this->request->getPost("access");
        

        if (!$project->save()) {

            foreach ($project->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "edit",
                "params" => array($project->id_project)
            ));
        }

        $this->flash->success("project was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "project",
            "action" => "index"
        ));

    }

    /**
     * Deletes a project
     *
     * @param string $id_project
     */
    public function deleteAction($id_project)
    {

        $project = Project::findFirstByid_project($id_project);
        if (!$project) {
            $this->flash->error("project was not found");

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        if (!$project->delete()) {

            foreach ($project->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "search"
            ));
        }

        $this->flash->success("project was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "project",
            "action" => "index"
        ));
    }


}
