<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for user
     */
    public function searchAction()
    {
  
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "User", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "id_user";

        $user = User::find($parameters);
        if (count($user) == 0) {
            $this->flash->notice("The search did not find any user");

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $user,
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
     * Edits a user
     *
     * @param string $id_user
     */
    public function editAction($id_user)
    {
        if (!$this->request->isPost()) {
            $user = User::findFirstByid_user($id_user);
            if (!$user) {
                $this->flash->error("user was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "user",
                    "action" => "index"
                ));
            }

            $this->view->id_user = $user->id_user;

            $this->tag->setDefault("id_user", $user->id_user);
            $this->tag->setDefault("login", $user->login);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("password", $user->password);
            
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $user = new User();

        $user->login = $this->request->getPost("login");
        $user->email = $this->request->getPost("email", "email");

        $password = $this->request->getPost("password");
        $user->password = $this->request->getPost("password");
        
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "new"
            ));
        }

        $this->flash->success("Votre inscription s'est déroulée correctement");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));

    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $id_user = $this->request->getPost("id_user");

        $user = User::findFirstByid_user($id_user);
        if (!$user) {
            $this->flash->error("Cet utilisateur n'existe pas " . $id_user);

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $user->login = $this->request->getPost("login");
        $user->email = $this->request->getPost("email", "email");
        $user->password = $this->request->getPost("password");
        

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "edit",
                "params" => array($user->id_user)
            ));
        }

        $this->flash->success("user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));

    }

    /**
     * Deletes a user
     *
     * @param string $id_user
     */
    public function deleteAction($id_user)
    {

        $user = User::findFirstByid_user($id_user);
        if (!$user) {
            $this->flash->error("user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        if (!$user->delete()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "search"
            ));
        }

        $this->flash->success("user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));
    }

    /**
     * Login a user
     *
     */
    public function loginAction()
    {
       

        if ($this->request->isPost()) {
            $login = $this->request->getPost("login");
            $password = $this->request->getPost("password");

            $user = User::findFirstByLogin($login);

            if ($user) {
                if ($password === $user->password) {
                //if($this->security->checkHash($password,$user->password)) {
                    $this->session->set('role', 'User');
                    $this->session->set('auth', $user->id_user);
                    $this->flash->success("Vous vous êtes correctement connecté.");
                    return $this->dispatcher->forward(array(
                        'controller' => 'project',
                        'action' => 'index'
                    ));
                } else {
                    $this->flash->error("Mot de passe incorrect.");
                    return $this->dispatcher->forward(array(
                        'controller' => 'user',
                        'action' => 'index'
                    ));
                }
            } else {
                 $this->flash->error("Cet utilisateur n'existe pas.");
                return $this->dispatcher->forward(array(
                    'controller' => 'user',
                    'action' => 'index'
                ));
            }
        }
    }

    /**
     * Logout a user
     *
     */
    public function logoutAction()
    {
        $this->session->remove('auth');
        $this->session->set('role', 'Guest');
        return $this->dispatcher->forward(array(
            'controller' => 'index',
            'action' => 'index'
        ));
    }
}
