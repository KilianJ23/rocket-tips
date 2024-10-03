<?php

class DefaultController extends AbstractController
{
    public function __construct() {
        // Call to the AbstractController so it can load twig
        parent::__construct();
    }

    public function homepage() : void
    {
        $am = new ArticlesManager();
        $articles = $am->getLastArticles(3);
        
        $this->render('front/home.html.twig', [
            'articles'      => $articles
            ], []);
    }
    
    public function notFound() : void
    {
        $this->render('front/error404.html.twig', [], []);
    }
}