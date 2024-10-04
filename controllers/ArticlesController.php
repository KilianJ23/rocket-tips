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
    
    // Method to show the creation page of an article
    
    public function newArticle() {
        
        $dc = new DefaultController();
        $dc->clearSessionMessages();        // Clear the Session messages
        $scripts = ['assets/js/image_size.js'];
        $this->render('front/admin/articles/newArticle.html.twig', [], $scripts);
        
    }
    
    // Method to create an article and push it in the database
    
    public function createArticle() {
        
        $dc = new DefaultController();
        $dc->clearSessionMessages();        // Clear the Session messages
        
        $dbc = new DashboardController();
        $dbc->checkArticleForm("newArticle");
        
        /*var_dump($dbc->checkArticleForm("createArticle"));
        die;*/
        $article = new Article($data["title"], $data["content"], $data["publish_date"], $data["level"], $data["description"]);
        
    }
    
    
    // Method to show the editing page for an article
    
    /*public function displayModifyArticle() {
        $articleId = $_GET['id'];
        $article = $this->am->getArticleById($articleId);
        
        $this->render('front/admin/articles/modifyArticle.html.twig', [
            'article'      => $article
        ], []);
        
        $dc = new DefaultController();
        $dc->clearSessionMessages();            // Clear the Session messages
    }*/
    
    // Method to display and edit an article and then update it in the database
    
    public function modifyArticle(): void
    {
        $dc = new DefaultController();
        $dc->clearSessionMessages();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $articleId = $_GET['id'];
            $article = $this->am->getArticleById($articleId);
            
            $this->render('front/admin/articles/modifyArticle.html.twig', [
                'article'      => $article
            ], []);

            return;
        }
                    // Clear the Session messages
        
        $id = $_GET['id'];
        
        if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['publish_date']) && isset($_POST['level']) && isset($_POST['csrf_token']) && isset($_POST['description'])) {
            
            $data = [
                'title'  => ucfirst(trim($_POST['title'])),           // Removing unnecessary spaces and uppercasing the first letter of the firstname, the rest in lowercase.           
                'content' => strip_tags($_POST['content'], '<p><br><strong>'),       // Removing unnecessary spaces and lowering the email
                'publish_date'    => trim($_POST['publish_date']),                      // Removing unnecessary spaces in the id
                'level'  => $_POST['level'],
                'description'  => $_POST['description'],
                'id' => $_GET['id']
            ];
            
            if(empty($data['title']) || empty($data['content']) || empty($data['publish_date']) || empty($data['level']) || empty($data['description']) ){
                $_SESSION['error_message'] = "Tous les champs doivent être remplis";
                $this->redirect("modifyArticle&id=$id");
            }
            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {
            
                $article = new Article($data["title"], $data["content"], $data["publish_date"], $data["level"], $data["description"]);
                $article->setId($data["id"]);
                
                // TO DO : GESTION DE L'IMAGE
                
                if(strlen($data['title']) > 255){
                    $_SESSION['error_message'] = "Le titre ne peut pas faire plus de 255 caractères";
                    $this->redirect("modifyArticle&id=$id");
                }
                
                if(strlen($data['description']) > 255){
                    $_SESSION['error_message'] += "La description ne peut pas faire plus de 255 caractères";
                    $this->redirect("modifyArticle&id=$id");
                }
            }
                    
            else {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect("modifyArticle&id=$id");
            }
        }
    }
    
    
}