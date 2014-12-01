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
        $this->checkAccess("index", null);
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
        $this->checkAccess("accept", null);
        $member= Member::findFirstByid_member($idmember);
        $member->status=1;
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
        $this->checkAccess("invit", null);
        $member=new Member();
        $member->id_user=$id_user;
        $member->id_project=$this->session->get("idproj");
        $member->status=2;
        if (!$member->save()) {
            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "member",
                "action" => "index"
            ));
        }

        $this->flash->success("L'invitaion a été envoyé");
        return $this->dispatcher->forward(array(
            "controller" => "member",
            "action" => "index"));
    }

    public function inscriptionAction($id_project)
    {
        $this->checkAccess("inscription", null);
        $project=Project::findFirst(array('id_project = ?0', 'bind' => array($id_project)));
        $member=new Member();
        $member->id_user=$this->session->get('auth');
        $member->id_project=$project->id_project;
        $member->status=3;
        if (!$member->save()) {
            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "project",
                "action" => "index"
            ));
        }

        $this->flash->success("L'inscription au projet a été effectué");
        return $this->dispatcher->forward(array(
            "controller" => "project",
            "action" => "index"));
    }

    public function myprojectAction()
    {
        $this->checkAccess("myproject", null);
        $numberPage=$this->request->getQuery("page", "int");

        $member=Member::query()->where("Member.id_user = :user:")
            ->bind(array("user" => $this->session->get("auth")))
            ->join('Project')
            ->execute();


        $paginator = new Paginator(array(
            "data" => $member,
            "limit"=> 10,
            "page" => $numberPage));

        $this->view->page = $paginator->getPaginate();
    }

    private function checkAccess($action, $id_project)
    {
        $role = $this->session->role;
        $access = array(
            "Guest" => array(),
            "User" => array("myproject", "accept", "inscription"),
            "Member" => array("index", "myproject"),
            "ScrumMaster" => array("index", "myproject", "accept", "invit")
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
