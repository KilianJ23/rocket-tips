<?php

class Router {

    private DefaultController $dc;
    private AuthController $ac;
    private AdminController $adc;
    private UserController $uc;
    private ArticlesController $arc;

    public function __construct() {
        $this->dc = new DefaultController();
        $this->ac = new AuthController();
        $this->adc = new AdminController();
        $this->uc = new UserController();
        $this->arc = new ArticlesController();
    }

    private function checkAdmin() : void {
        if(!isset($_SESSION['user']))
        {
            header('Location: index.php?route=admin-connexion');
        }

        if($_SESSION['user']->getRole() !== "ADMIN")
        {
            header('Location: index.php?route=admin-connexion');
        }
    }

    public function handleRequest(? string $route) : void {
        if($route === null || $route ==="home")
        {
            // le code si il n'y a pas de route ( === la page d'accueil)
            $this->dc->homepage();
        }
        
                                            //Routes CONNEXION/INSCRIPTION
        
        else if($route === "inscription")
        {
            $this->ac->displayRegister();
        }
        else if($route === "check-inscription")
        {
            $this->ac->checkRegister();
        }
        else if($route === "connexion")
        {
            $this->ac->login();
        }
        else if($route === "check-connexion")
        {
            $this->ac->checkLogin();
        }
        else if($route === "deconnexion")
        {
            $this->ac->logout();
        }
        else if($route === "admin")
        {
            $this->checkAdmin();
            $this->adc->home();
        }
        else if($route === "admin-connexion")
        {
            $this->adc->login();
        }
        else if($route === "admin-check-connexion")
        {
            $this->adc->checkLogin();
        }
        
                                            //Routes pour GESTION USERS
        
        else if($route === "admin-create-user")
        {
            $this->checkAdmin();
            $this->uc->create();
        }
        else if($route === "admin-check-create-user")
        {
            $this->checkAdmin();
            $this->uc->checkCreate();
        }
        else if($route === "admin-edit-user" && isset($_GET["user_id"]))
        {
            $this->checkAdmin();
            $this->uc->edit(intval($_GET["user_id"]));
        }
        else if($route === "admin-check-edit-user" && isset($_GET["user_id"]))
        {
            $this->checkAdmin();
            $this->uc->checkEdit(intval($_GET["user_id"]));
        }
        else if($route === "admin-delete-user" && isset($_GET["user_id"]))
        {
            $this->checkAdmin();
            $this->uc->delete(intval($_GET["user_id"]));
        }
        else if($route === "admin-list-users")
        {
            $this->checkAdmin();
            $this->uc->list();
        }
        else if($route === "admin-show-user" && isset($_GET["user_id"]))
        {
            $this->checkAdmin();
            $this->uc->show(intval($_GET["user_id"]));
        }
        
                                                    //Routes pour GESTION ARTICLES
        
        else if($route === "articles")
        {
            $this->arc->displayArticles();
        }
        else
        {
            // le code si c'est aucun des cas précédents ( === page 404)
            $this->dc->notFound();
        }
    }
}