<?php

class ArticlesController extends AbstractController
{
    private UserManager $um;

    public function __construct() {
        parent::__construct();
        $this->um = new UserManager();
    }

    //Method displayArticles() : Displays the page with all the articles

    public function displayArticles() {
        $am = new ArticlesManager;
        
        // Calculating the total number of pages, we can adjust the parameter $articles_par_page to choose the number of articles to display
        
        $articles_par_page = 6;
        $page_actuelle = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page_actuelle - 1) * $articles_par_page;
        
        // If no difficulty level is selected we display articles of all levels
        
        if (!isset($_GET['level'])) {
        
        $total_articles = $am->getCountAll()['COUNT(*)'];
        $total_pages = ceil($total_articles / $articles_par_page);
        
        $page_actuelle = min($page_actuelle, $total_pages);
        
        $articles = $am->getArticlesParPage($articles_par_page, $offset);
        
        $this->render('front/articles/articles.html.twig', [
            'articles'      => $articles,
            'page_actuelle' => $page_actuelle,
            'total_pages'   => $total_pages,
        ]);
        
        }
        
        // If the level is set, then we only display the articles with the matching level
        
        else {
            $lm = new LevelsManager;
            
            $level = $_GET['level'];
            $levelName = $lm->getLevelName($level);
            $total_articles = $am->getCountAllByLevel($level)['COUNT(*)'];
            $total_pages = ceil($total_articles / $articles_par_page);
            
            $page_actuelle = min($page_actuelle, $total_pages);
            $articles = $am->getArticlesByPageAndLevel($articles_par_page, $offset, $level);
            
            $this->render('front/articles/articles.html.twig', [
            'articles'      => $articles,
            'page_actuelle' => $page_actuelle,
            'total_pages'   => $total_pages,
            'level'         => $level,
            'level_name'    => $levelName
        ]);
        }
    }
    
    
    // Method to display a single article
    
    public function displayArticle() {
        $am = new ArticlesManager;
        
        //Ré
        $articleId = $_GET['id'];
        
        $article = $am->getArticleById($articleId);
        
        $this->render('front/articles/article.html.twig', [
            'article'      => $article
        ]);
    }
    
    // Method to display all articles in the admin panel
    
    public function displayManageArticles() {
        $am = new ArticlesManager();
        $articles = $am->getAll();
        
        $this->render('front/admin/articles/allArticles.html.twig', [
            "articles" => $articles]);
    }
}