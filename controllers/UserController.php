<?php

class UserController extends AbstractController
{
    private UserManager $um;
    public function __construct(){
        parent::__construct();
        $this->um = new UserManager();
    }

    public function create() : void {
        $this->render("admin/users/create.html.twig", [], []);
    }

    public function checkCreate() : void {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }

        if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['csrf_token']) && isset($_POST["role"])) {

            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {

                $user = $this->um->findUserByEmail($_POST['email']);

                if($user === null)
                {
                    if($_POST['password'] === $_POST['confirm_password'])
                    {
                        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        $user = new User($_POST['email'], $_POST['name'], $password, $_POST["role"]);

                        try {
                            $this->um->createUser($user);
                            $_SESSION['success_message'] = "L'utilisateur a bien été créé";
                            $this->redirect('admin-list-users');
                        }
                        catch(\Exception $e)
                        {
                            $_SESSION['error_message'] = $e->getMessage();
                            $this->redirect('admin-create-user');
                        }
                    }
                    else
                    {
                        $_SESSION['error_message'] = "Les mots de passe ne correspondent pas.";
                        $this->redirect('admin-create-user');
                    }
                }
                else
                {
                    $_SESSION['error_message'] = "Un compte existe déjà avec cette adresse.";
                    $this->redirect('admin-create-user');
                }
            }
            else
            {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect('admin-create-user');
            }
        }
        else
        {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            $this->redirect('admin-create-user');
        }
    }

    public function edit(int $id) : void {
        $user = $this->um->findUserById($id);

        $this->render("admin/users/edit.html.twig", [
            "user" => $user
        ], []);
    }

    public function checkEdit(int $id) : void {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }

        if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['csrf_token']) && isset($_POST["role"])) {

            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {

                $user = $this->um->findUserById($id);

                if($user !== null)
                {
                    if($_POST['password'] === $_POST['confirm_password'])
                    {
                        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        $user = new User($_POST['email'], $password, $_POST["role"]);
                        $user->setId($id);

                        try {
                            $this->um->updateUser($user);
                            $_SESSION['success_message'] = "L'utilisateur a bien été modifié";
                            $this->redirect('admin-list-users');
                        }
                        catch(\Exception $e)
                        {
                            $_SESSION['error_message'] = $e->getMessage();
                            $this->redirect("admin-edit-user&user_id=$id");
                        }
                    }
                    else
                    {
                        $_SESSION['error_message'] = "Les mots de passe ne correspondent pas.";
                        $this->redirect("admin-edit-user&user_id=$id");
                    }
                }
                else
                {
                    $_SESSION['error_message'] = "Un compte existe déjà avec cette adresse.";
                    $this->redirect("admin-edit-user&user_id=$id");
                }
            }
            else
            {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect("admin-edit-user&user_id=$id");
            }
        }
        else
        {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            $this->redirect("admin-edit-user&user_id=$id");
        }
    }

    
    // Method deleteUser() : Completely erases the user from the database
    
    public function deleteUser(): void
    {
        $userId = $_GET['id'];
        $this->um->deleteUser($userId);
        
        $this->displayUsers();
    }
    
    // Method modifyUser() : Updates the data regarding a user in the database
    
    public function modifyUser(): void
    {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
        
        $id = $_GET['id'];
        
        if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['id']) && isset($_POST['role'])) {
            $user = new User($_POST["name"], $_POST["email"], "password", $_POST["role"]);
            $user->setId($_POST["id"]);
            
            $user2 = $this->um->findUserByEmail($_POST['email']);
            
            if($user2 === null) {
                $this->um->updateUser($user);
                $_SESSION['success_message'] = "L'utilisateur a bien été modifié";
                $this->redirect("modifyUser&id=$id");
            }
            else {
                $_SESSION['error_message'] = "Un utilisateur possède déjà cette adresse e-mail";
                $this->redirect("modifyUser&id=$id");
            }
        }
        else {
                $_SESSION['error_message'] = "Tous les champs doivent être remplis";
                $this->redirect("modifyUser&id=$id");
            }
    }
}