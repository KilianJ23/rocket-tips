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
        $this->fc = new ForumController();
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

    public function handleRequest(?string $route): void {
    switch ($route) {
        case null:
        case "home":
            // Page d'accueil
            $this->dc->homepage();
            break;

        // Routes CONNEXION/INSCRIPTION
        case "inscription":
            $this->ac->displayRegister();
            break;

        case "check-inscription":
            $this->ac->checkRegister();
            break;

        case "connexion":
            $this->ac->login();
            break;

        case "check-connexion":
            $this->ac->checkLogin();
            break;

        case "deconnexion":
            $this->ac->logout();
            break;

        // Routes pour ADMIN
        case "admin":
            $this->checkAdmin();
            $this->adc->home();
            break;

        case "admin-connexion":
            $this->adc->login();
            break;

        case "admin-check-connexion":
            $this->adc->checkLogin();
            break;

        // Routes pour GESTION USERS
        case "admin-create-user":
            $this->checkAdmin();
            $this->uc->create();
            break;

        case "admin-check-create-user":
            $this->checkAdmin();
            $this->uc->checkCreate();
            break;

        case "admin-edit-user":
            if (isset($_GET["user_id"])) {
                $this->checkAdmin();
                $this->uc->edit(intval($_GET["user_id"]));
            }
            break;

        case "admin-check-edit-user":
            if (isset($_GET["user_id"])) {
                $this->checkAdmin();
                $this->uc->checkEdit(intval($_GET["user_id"]));
            }
            break;

        case "admin-delete-user":
            if (isset($_GET["user_id"])) {
                $this->checkAdmin();
                $this->uc->delete(intval($_GET["user_id"]));
            }
            break;

        case "admin-list-users":
            $this->checkAdmin();
            $this->uc->list();
            break;

        case "admin-show-user":
            if (isset($_GET["user_id"])) {
                $this->checkAdmin();
                $this->uc->show(intval($_GET["user_id"]));
            }
            break;

        // Routes pour GESTION ARTICLES
        case "articles":
            if (isset($_GET["page"])) {
                $this->arc->displayArticles();
            } elseif (isset($_GET["level"])) {
                $this->arc->displayArticles();
            } elseif (isset($_GET["id"])) {
                $this->arc->displayArticle();
            } else {
                $this->arc->displayArticles();
            }
            break;
            
        case "forum":
            $this->fc->displayForum();
            break;

        // Page 404
        default:
            $this->dc->notFound();
            break;
    }
}

}