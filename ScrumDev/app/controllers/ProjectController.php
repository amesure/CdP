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
        $this->checkAccess("index", null);

        $numberPage = $this->request->getQuery("page", "int");
        $project = Project::query()->where("Project.access=1")->execute();

        $paginator = new Paginator(array(
            "data" => $project,
            "limit"=> 10,
            "page" => $numberPage));

        $this->view->page = $paginator->getPaginate();

        $this->session->remove('id_proj');
        $this->session->remove('perm');
        if ($this->session->get('auth')) {
            $this->session->set('role', 'User');
        }
    }


    public function showAction($id_project)
    {
        $this->checkAccess("show", $id_project);

        $project=Project::findFirstByid_project($id_project);
        $member = Member::query()
            ->where("id_project = :idpro:")
            ->andWhere("id_user=:iduser:")
            ->bind(array("idpro" => $id_project,"iduser"=>$this->session->get("auth")))
            ->execute();

        $this->session->set('id_proj', $project->id_project);

        if ($member->count() > 0) {
            $status = $member->getFirst()->status;
            $this->session->set('perm', $status);
            if ($status == 0) {
                $this->session->set('role', "ScrumMaster");
            }
            if ($status == 1) {
                $this->session->set('role', "Member");
            }
        }

        $this->view->member=$member;
        $this->view->project=$project;

        $project=Project::findFirstByid_project($id_project);
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


    /*
     * Displayes the creation form
     */
    public function newAction()
    {
        $this->checkAccess("new", null);
    }

    /**
     * Edits a project
     *
     * @param string $id_project
     */
    public function editAction($id_project)
    {
        $this->checkAccess("edit", $id_project);

        $member = Member::findFirst(array(
            'id_user = ?0 and id_project = ?1',
            'bind' => array(
                $this->session->get("auth"),
                $id_project)
        ));

        if (!$member) {
            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        if (!$this->request->isPost()) {
            $project = Project::findFirstByid_project($id_project);
            if (!$project) {
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
            $this->flash->error("Vous n'avez pas accès à cette page.");
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

        $member= new Member();
        $member->id_project=$project->id_project;
        $member->id_user=$this->session->get('auth');
        $member->status=0;
        if (!$member->save()) {
            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "new"
            ));
        }

        $this->flash->success("Le projet a été créé");
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
            $this->flash->error("Ce projet n'existe pas " . $id_project);

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $project->title = $this->request->getPost("title");
        $project->content = $this->request->getPost("content");
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
        if (!$this->request->isPost()) {
            $this->flash->error("Vous n'avez pas accès à cette page.");
            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $project = Project::findFirstByid_project($id_project);
        $member = Member::find(array(' id_project = ?0', 'bind' => array($id_project)));
        if (!$project) {
            $this->flash->error("project was not found");

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        if (!$member->delete()) {
            foreach ($project->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "search"
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

    private function checkAccess($action, $id_project)
    {
        $role = $this->session->role;
        $access = array(
            "Guest" => array("index"),
            "User" => array("index", "new", "show"),
            "Member" => array("index", "new", "show", "edit"),
            "ScrumMaster" => array("index", "new", "show", "edit", "delete")
            );

        if (array_search($action, $access[$role]) === false) {
            $this->flash->error("Vous n'avez pas accès à cette page.");

            return $this->dispatcher->forward(array(
                "controller" => "index",
                "action" => "index"
            ));
        }
    }
}
