<?php

class ArticlesController extends AbstractController
{
    private UserManager $um;

    public function __construct() {
        parent::__construct();
        $this->um = new UserManager();
    }

    //Méthode de displayArticles() : Affiche la page globale des articles.

    public function displayArticles() : void {
        
        $am = new ArticlesManager;
        //$articles = (new ArticlesManager())->getAll();
        
        $articles_par_page = 6;
        $page_actuelle = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page_actuelle - 1) * $articles_par_page;
        
        // Requête pour obtenir le nombre total d'articles
        $compte = $am->getCountAll();
        $total_articles = $compte['COUNT(*)'];
        
        
        // Calcul du nombre total de pages
        $total_pages = ceil($total_articles / $articles_par_page);
        
        // Requête pour obtenir les articles de la page actuelle
        $articles = $am->getArticlesParPage($articles_par_page, $offset);
        
        $this->render('front/articles/articles.html.twig', [
            'articles' => $articles,
            'page_actuelle' => $page_actuelle,
            'total_pages' => $total_pages]);
        
    }
}