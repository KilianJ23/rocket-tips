<?php

class AdminController extends AbstractController
{
    private UserManager $um;
    public function __construct(){
        parent::__construct();
        $this->um = new UserManager();
    }

    public function home() : void {
        $this->render('admin/home.html.twig', [], []);
    }

    public function login() : void {
        $this->render('admin/login.html.twig', [], []);
    }

    public function checkLogin() : void {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }

        if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['csrf_token'])) {

            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {

                $user = $this->um->findUserByEmail($_POST['email']);

                if($user !== null)
                {
                    if($user->getRole() === 'ADMIN')
                    {
                        if(password_verify($_POST['password'], $user->getPassword()))
                        {
                            $_SESSION['user'] = $user;
                            $this->redirect('admin');
                        }
                        else
                        {
                            $_SESSION['error_message'] = "Identifiant ou mot de passe incorrect.";
                            $this->redirect('admin-connexion');
                        }
                    }
                    else
                    {
                        $_SESSION['error_message'] = "Vous n'avez pas les droits suffisants pour accéder à cette page";
                        $this->redirect('admin-connexion');
                    }
                }
                else
                {
                    $_SESSION['error_message'] = "Identifiant ou mot de passe incorrect.";
                    $this->redirect('admin-connexion');
                }
            }
            else
            {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect('admin-connexion');
            }
        }
        else
        {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            $this->redirect('admin-connexion');
        }
    }
}