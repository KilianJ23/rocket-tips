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
    
    public function clearSessionMessages(): void
    {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
        $_SESSION['error_message'] = [];
        $_SESSION['success_message'] = [];
    }
}