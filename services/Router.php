<?php

class Router {

    private DefaultController $dc;
    private AuthController $ac;
    private AdminController $adc;
    private UserController $uc;
    private ArticlesController $arc;
    private ForumController $fc;
    private DashboardController $dbc;

    public function __construct() {
        $this->dc = new DefaultController();
        $this->ac = new AuthController();
        $this->adc = new AdminController();
        $this->uc = new UserController();
        $this->arc = new ArticlesController();
        $this->fc = new ForumController();
        $this->dbc = new DashboardController();
    }

    private function checkAdmin() : void {
        if(!isset($_SESSION['user']))
        {
            header('Location: index.php?route=connexion');
        }

        if($_SESSION['user']->getRole() !== "ADMIN")
        {
            header('Location: index.php?route=home');
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


    // Routes pour ARTICLES
        
        case "articles":
            if (isset($_GET["page"])) {
                $this->arc->displayArticles();
            } elseif (isset($_GET["level"])) {
                $this->arc->displayArticles();
            } else {
                $this->arc->displayArticles();
            }
            break;
        
        case "article":
            if (isset($_GET["id"])) {
                $this->arc->displayArticle();
            }
            break;
        
        
    // Routes du DASHBOARD ADMIN
        
        case "dashboard":
            $this->checkAdmin();
            $this->dbc->displayDashboard();
            break;
        
        
    // Pour USERS
        
        case "allUsers":
            $this->checkAdmin();
            $this->dbc->displayUsers();
            break;
            
        case "showUser":
            $this->checkAdmin();
            if (isset($_GET["id"])) {
                $this->uc->modifyUser();
            }
            break;
        
        case "deleteUser":
            $this->checkAdmin();
            if (isset($_GET["id"])) {
                $this->uc->deleteUser();
            }
            break;
        
        
    // Pour ARTICLES
        
        case "manageArticles":
            $this->checkAdmin();
            $this->arc->displayManageArticles();
            break;
            
        case "newArticle":
            $this->checkAdmin();
            $this->arc->newArticle();
            break;
        
        case "createArticle":
            $this->checkAdmin();
            $this->arc->createArticle();
            break;
            
        case "modifyArticle":
            $this->checkAdmin();
            if (isset($_GET["id"])) {
                $this->arc->modifyArticle();
            }
            break;
        
        case "deleteArticle":
            $this->checkAdmin();
            if (isset($_GET["id"])) {
                $this->arc->deleteArticle();
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