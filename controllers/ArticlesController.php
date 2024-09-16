<?php

class ArticlesController extends AbstractController
{
    private UserManager $um;

    public function __construct() {
        parent::__construct();
        $this->um = new UserManager();
    }

    //Méthode de displayArticles() : Affiche la page globale des articles.

    public function displayArticles() {
        $am = new ArticlesManager;
        
        // Calcul du nombre de pages total, on peut jouer sur le paramètre $articles_par_page pour choisir le nombre d'articles à afficher
        
        $articles_par_page = 6;
        $page_actuelle = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page_actuelle - 1) * $articles_par_page;
        
        // Si le level de difficulté n'est pas sélectionné nous affichons les articles de tous niveaux
        
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
        
        // Si le level est renseigné, nous affichons uniquement les articles ayant le niveau correspondant
        
        else {
            $level = $_GET['level'];
            $levelName = $am->getLevelName($level);
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
}