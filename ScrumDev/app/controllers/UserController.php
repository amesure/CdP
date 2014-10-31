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
        echo "toto";
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

        if($this->request->getPost("login")=="" 
            || $this->request->getPost("email", "email")==""
            || $this->request->getPost("password")=="") {

            $this->flash->error("Aucun champs ne peut être vide.");
            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "new"
            ));
        }
        echo "toto";
        $user = new User();

        $user->login = $this->request->getPost("login");
        $user->email = $this->request->getPost("email", "email");
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

        $this->flash->success("user was created successfully");

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
            $this->flash->error("user does not exist " . $id_user);

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

            $user = User::findFirst(array(
                'login = :login: and password = :password:',
                'bind' => array(
                    'login' => $this->request->getPost("login"),
                    'password' => $this->request->getPost("password")
                )
            ));

            if ($user === false){
                $this->flash->error("Incorrect credentials");
                return $this->dispatcher->forward(array(
                    'controller' => 'user',
                    'action' => 'index'
                ));
            }

            $this->session->set('auth', $user->id);

            $this->flash->success("You've been successfully logged in");
        }

        return $this->dispatcher->forward(array(
            'controller' => 'project',
            'action' => 'index'
        ));
    }

    /**
     * Logout a user
     *
     */
    public function logoutAction()
    {
        $this->session->remove('auth');
        return $this->dispatcher->forward(array(
            'controller' => 'index',
            'action' => 'index'
        ));
    }

}
