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
        
        $dc = new DefaultController();
        $dc->clearSessionMessages();
        
        $articleId = $_GET['id'];
        
        $article = $this->am->getArticleById($articleId);
        $cm = new CommentsManager();
        $comments = $cm->getCommentsByArticleId($articleId);
        
        // Case where no comment has been submitted
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            
            $this->render('front/articles/article.html.twig', [
            'article'      => $article,
            'comments' => $comments
             ], []);

            return;
        }
        
        // Case where a comment has just been submitted
        
        else if (!empty($_POST['comment_content'])) {
            
            $commentContent = trim($_POST['comment_content']);  //Removing unnecessary spaces in the content 
            $userId = $_SESSION['user']->getId();
            $cm->addComment($articleId, $commentContent, $userId);
        }
        
        else {
            $_SESSION['error_message'][] = "Le commentaire ne peut être vide.";
            $this->redirect("article&id=$articleId");
        }
        
        // Re loading the comments in case one has been added
        
        $comments = $cm->getCommentsByArticleId($articleId);
        
        $this->render('front/articles/article.html.twig', [
            'article'      => $article,
            'comments' => $comments
        ], []);
    }
    
    // Method to display all articles in the admin panel
    
    public function displayManageArticles() {
        $articles = $this->am->getAll();
        
        $this->render('front/admin/articles/allArticles.html.twig', [
            "articles" => $articles], []);
            
        $dc = new DefaultController();
        $dc->clearSessionMessages();
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
        $data = [];
        
        $dbc = new DashboardController();
        $data = $dbc->checkArticleForm("newArticle");
        
        $article = new Article($data["title"], $data["content"], $data["publish_date"], $data["level"], $data["description"]);
        
        if(isset($_FILES['imag_article']) && $_FILES['imag_article']['name'] !== '') {
            // Setting the variables we are gonna use in the uploadFile method
                $dossier = "img_Of_Articles";
                $route = "newArticle";
                $maxFileSize = 2 * 1024 * 1024; // 2 Mo
                
            // CHECK that the FILE SIZE is under the limit that we set with $maxFileSize.       
                if ($_FILES['imag_article']['size'] > $maxFileSize) {
                    $_SESSION['error_message'][] = "L'image est trop volumineuse. La taille maximale autorisée est de 2 Mo.";
                    $this->redirect("newArticle");
                }
                    
                $model = new Uploads();
                $addArticle['addImg'] = $model->uploadFile($_FILES['imag_article'], $dossier, $route);
                // $addArticle['addImg'] = $model->upload($_FILES['imag_article'], $dossier, 'public/uploads/', ["pdf"]);
        }
        else {
            $_SESSION['error_message'][] = "Aucun fichier n'a été envoyé";
            $this->redirect("newArticle");
        }
        
        /*var_dump($addArticle, $_SESSION['error_message']);
        die;*/
        
        // All checks have been cleared, we can insert the new Article in the DB
        $newArticle = $this->am->createArticle($article);  // We get the newly inserted Article to use its ID to insert the media.
        
        $mm = new MediasManager();
        $totalMedias = $mm->getCountAll();
        $mm->addMedia($addArticle['addImg'], $data['alt_description'], $newArticle->getId(), "article", $newArticle->getPublishDate());
        
        $totalMediasFinal = $mm->getCountAll();
        
        if($totalMediasFinal > $totalMedias){
            $_SESSION['success_message'][] = "L'article a été enregistré !";
        }
        else {
            $_SESSION['error_message'] = "Toutes les vérifications sont OK mais l'image n'a pu être enregistrée.";
        }
        
        $this->redirect("newArticle");
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
        // Clear the Session messages
        $dc = new DefaultController();
        $dc->clearSessionMessages();
        
        // If the page was loaded without a form submit, we simply render the template
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $articleId = $_GET['id'];
            $article = $this->am->getArticleById($articleId);
            
            $this->render('front/admin/articles/modifyArticle.html.twig', [
                'article'      => $article
            ], []);

            return;
        }
        
        $id = trim($_GET['id']);
        $data = [];
        
        // CHECK that the form is valid - method in DashboardController
        $dbc = new DashboardController();
        $data = $dbc->checkArticleForm("modifyArticle&id=$id");
        
        $article = new Article($data["title"], $data["content"], $data["publish_date"], $data["level"], $data["description"]);
        $article->setId($id);
        
        //CHECK that the image given, if there is one, is valid
        if(isset($_FILES['imag_article']) && $_FILES['imag_article']['name'] !== '') {
            // Setting the variables we are gonna use in the uploadFile method
                $dossier = "img_Of_Articles";
                $route = "modifyArticle&id=$id";
                $maxFileSize = 2 * 1024 * 1024; // 2 Mo
                
            // CHECK that the FILE SIZE is under the limit that we set with $maxFileSize.       
                if ($_FILES['imag_article']['size'] > $maxFileSize) {
                    $_SESSION['error_message'][] = "L'image est trop volumineuse. La taille maximale autorisée est de 2 Mo.";
                    $this->redirect("modifyArticle&id=$id");
                }
                    
                $model = new Uploads();
                $addArticle['addImg'] = $model->uploadFile($_FILES['imag_article'], $dossier, $route);
                
                // GET the already existing media ID
                
                $mm = new MediasManager();
                $mediaId = $mm->getIdFromOwner($id, "article");
                
                // IF the Media exists -> Update it, if it doesn't existe -> Create one.
                
                if($mediaId != "null"){
                    $resultMedia = $mm->updateMedia($mediaId, $addArticle['addImg'], $data['alt_description'], $id, "article", $article->getPublishDate());
                }
                else {
                    $resultMedia = $mm->addMedia($addArticle['addImg'], $data['alt_description'], $id, "article", $article->getPublishDate());
                }

                if($resultMedia != "true"){
                    $_SESSION['error_message'][] = "L'image n'a pas pu être importée en Base de données";
                    $this->redirect("modifyArticle&id=$id");
                }
        }
        
        $newArticle = $this->am->updateArticle($article);
        
        if($newArticle != "true"){
            $_SESSION['error_message'][] = "L'article n'a pas pu être modifié, l'image a bien été changée.";
        }
        else {
            $_SESSION['success_message'][] = "L'article et l'image ont bien été modifiés !";
        }
        $this->redirect("modifyArticle&id=$id");
    }
    
    
    // Method deleteArticle() : Completely erases the article and its associated media from the database
    
    public function deleteArticle(): void
    {
        // Clear the Session messages
        $dc = new DefaultController();
        $dc->clearSessionMessages();
        
        $articleId = $_GET['id'];
        $type = "article";
        
        $this->am->deleteArticleAndMedia($articleId, $type);
        
        $_SESSION['success_message'][] = "L'article a été supprimé";
        
        $this->redirect("manageArticles");
    }
}