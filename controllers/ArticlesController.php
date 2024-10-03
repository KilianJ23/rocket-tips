<?php

class ArticlesController extends AbstractController
{
    private UserManager $um;

    public function __construct() {
        parent::__construct();
        $this->am = new ArticlesManager();
    }

    //Method displayArticles() : Displays the page with all the articles

    public function displayArticles() {
        $scripts = ['assets/js/pagination_articles.js'];
        // Calculating the total number of pages, we can adjust the parameter $articles_par_page to choose the number of articles to display
        
        $articles_par_page = 6;
        $page_actuelle = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page_actuelle - 1) * $articles_par_page;
        
        // If no difficulty level is selected we display articles of all levels
        
        if (!isset($_GET['level'])) {
        
        $total_articles = $this->am->getCountAll()['COUNT(*)'];
        $total_pages = ceil($total_articles / $articles_par_page);
        
        $page_actuelle = min($page_actuelle, $total_pages);
        
        $articles = $this->am->getArticlesParPage($articles_par_page, $offset);
        
        $this->render('front/articles/articles.html.twig', [
            'articles'      => $articles,
            'page_actuelle' => $page_actuelle,
            'total_pages'   => $total_pages,
        ], $scripts);
        
        }
        
        // If the level is set, then we only display the articles with the matching level
        
        else {
            $lm = new LevelsManager;
            
            $level = $_GET['level'];
            $levelName = $lm->getLevelName($level);
            $total_articles = $this->am->getCountAllByLevel($level)['COUNT(*)'];
            $total_pages = ceil($total_articles / $articles_par_page);
            
            $page_actuelle = min($page_actuelle, $total_pages);
            $articles = $this->am->getArticlesByPageAndLevel($articles_par_page, $offset, $level);
            
            $this->render('front/articles/articles.html.twig', [
            'articles'      => $articles,
            'page_actuelle' => $page_actuelle,
            'total_pages'   => $total_pages,
            'level'         => $level,
            'level_name'    => $levelName
        ], $scripts);
        }
    }
    
    
    // Method to display a single article
    
    public function displayArticle() {
        $articleId = $_GET['id'];
        
        $article = $this->am->getArticleById($articleId);
        
        $this->render('front/articles/article.html.twig', [
            'article'      => $article
        ], []);
    }
    
    // Method to display all articles in the admin panel
    
    public function displayManageArticles() {
        $articles = $this->am->getAll();
        
        $this->render('front/admin/articles/allArticles.html.twig', [
            "articles" => $articles], []);
    }
    
    // Method to display all articles in the admin panel
    
    public function displayModifyArticle() {
        $articleId = $_GET['id'];
        $article = $this->am->getArticleById($articleId);
        
        $this->render('front/admin/articles/modifyArticle.html.twig', [
            'article'      => $article
        ], []);
        
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
    }
    
    // Method to edit an article and update it in the database
    
    public function modifyArticle(): void
    {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
        
        $id = $_GET['id'];
        
        if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['publish_date']) && isset($_POST['level']) && isset($_POST['csrf_token']) && isset($_POST['description'])) {
            
            $data = [
                'title'  => ucfirst(trim($_POST['name'])),           // Removing unnecessary spaces and uppercasing the first letter of the firstname, the rest in lowercase.           
                'content' => strtolower(trim($_POST['email'])),       // Removing unnecessary spaces and lowering the email
                'publish_date'    => trim($_POST['id']),                      // Removing unnecessary spaces in the id
                'level'  => $_POST['role'],
                'description'  => $_POST['role']
            ];
            
            if(empty($data['title']) || empty($data['content']) || empty($data['publish_date']) || empty($data['level']) || empty($data['description']) ){
                $_SESSION['error_message'] = "Tous les champs doivent être remplis";
                $this->redirect("displayModifyArticle&id=$id");
            }
            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {
            
                $article = new Article($data["title"], $data["content"], "password", $data["role"]);
                $article->setId($data["id"]);
                
                $user2 = $this->um->findUserByEmail($data['email']);
                
                
                if(($user2 === null) || $user2->getId() === $user->getId()) {
                    
                    $user3 = $this->um->findUserByName($data['name']);
                    
                    if(($user3 === null) || $user3->getId() === $user->getId()) {
                    
                    $this->um->updateUser($user);
                    $_SESSION['success_message'] = "L'utilisateur a bien été modifié";
                    $this->redirect("showUser&id=$id");
                    }
                    
                    else {
                    $_SESSION['error_message'] = "Un utilisateur possède déjà ce nom";
                    $this->redirect("showUser&id=$id");
                    }
                }
                else {
                    $_SESSION['error_message'] = "Un utilisateur possède déjà cette adresse e-mail";
                    $this->redirect("showUser&id=$id");
                }
            }
            else {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect("showUser&id=$id");
            }
        }
        else {
            $_SESSION['error_message'] = "Tous les champs doivent être remplis";
            $this->redirect("showUser&id=$id");
        }
    }
}