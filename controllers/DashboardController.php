<?php

class DashboardController extends AbstractController
{
    private UserManager $um;

    public function __construct() 
    {
        parent::__construct();
        $this->um = new UserManager();
    }

    // Method displayDashboard() : Renders the Dashboard landing page.

    public function displayDashboard(): void
    {
        $this->render('front/admin/dashboard.html.twig', [], []);
    }
    
    // Method displayUsers() : Shows all the users
    
    public function displayUsers(): void
    {
        $users = $this->um->getAllUsers();
        
        $this->render('front/admin/users/users.html.twig', [
            "users" => $users], []);
    }
    
    
    
    public function checkArticleForm(string $route) {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
        
        $_SESSION['error_message'] = [];
        $_SESSION['success_message'] = [];
        
        if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['publish_date']) && isset($_POST['level']) && isset($_POST['csrf_token']) && isset($_POST['description']) && isset($_POST['alt_description'])) {
            
            $data = [
                'title'  => ucfirst(trim($_POST['title'])),                          // Removing unnecessary spaces and uppercasing the first letter of the firstname, the rest in lowercase.           
                'content' => strip_tags($_POST['content'], '<p><br><strong>'),       // Removing unnecessary spaces and unwanted HTML/PHP
                'publish_date'    => trim($_POST['publish_date']),                   // Removing unnecessary spaces in the date
                'level'  => trim($_POST['level']),
                'description'  => strip_tags($_POST['description']),
                'alt_description'  => strip_tags($_POST['alt_description'])
            ];
            
            if(empty($data['title']) || empty($data['content']) || empty($data['publish_date']) || empty($data['level']) || empty($data['description']) || empty($data['alt_description']) ){
                $_SESSION['error_message'][] = "Tous les champs doivent être remplis";
                $this->redirect($route);
            }
            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {
            
                // TO DO : GESTION DE L'IMAGE
                
                if(strlen($data['title']) > 255){
                    
                    $_SESSION['error_message'][] = "Le titre ne peut pas faire plus de 255 caractères";
                    
                    if(strlen($data['description']) > 255){
                        
                        $_SESSION['error_message'][] = "La description ne peut pas faire plus de 255 caractères";
                
                        if(strlen($data['alt_description']) > 255){
                            
                            $_SESSION['error_message'][] = "La description de l'image ne peut pas faire plus de 255 caractères";
                        }
                    }
                    $this->redirect($route);
                }
                
                return $data;
            }
                    
            else {
                $_SESSION['error_message'][] = "Le jeton CSRF est invalide.";
                $this->redirect($route);
            }
        }
    }
    
    // Method to display the comments of an article
    
    public function displayComments(): void {
        
        $articleId = $_GET['id'];
        $am = new ArticlesManager();
        $article = $am->getArticleById($articleId);
        
        $cm = new CommentsManager();
        $comments = $cm->getCommentsByArticleId($articleId);
        
        $this->render('front/admin/articles/comments.html.twig', [
            'comments' => $comments,
            'article' => $article
        ], []);
        
        $dc = new DefaultController();
        $dc->clearSessionMessages();        // Clear the Session messages
    }
    
    // Method to delete a comment from an article
    
    public function deleteComment(): void {
        
        // Clear the Session messages
        $dc = new DefaultController();
        $dc->clearSessionMessages();
        
        $cm = new CommentsManager();
        
        $id = $_GET['id'];
        $commentId = $_GET['commentid'];
        
        if(($cm->deleteComment($commentId)) === true) {
            $_SESSION['success_message'][] = "Le commentaire a été supprimé";
        }
        
        else {
            $_SESSION['error_message'][] = "Le commentaire n'a pas pu être supprimé";
        }
        
        $this->redirect("articleComments&id=$id");
    }
}