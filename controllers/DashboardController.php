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

    public function displayDashboard() 
    {
        $this->render('front/admin/dashboard.html.twig', [], []);
    }
    
    // Method displayUsers() : Shows all the users
    
    public function displayUsers() 
    {
        $users = $this->um->getAllUsers();
        
        $this->render('front/admin/users/users.html.twig', [
            "users" => $users], []);
    }
    
    // Method displayUser() : Shows the information of a single user
    
    /*public function displayUser() 
    {
        $userId = $_GET['id'];
        $user = $this->um->getUserById($userId);
        
        $this->render('front/admin/users/modifyUser.html.twig', [
            'user'      => $user
        ], []);
        
        $dc = new DefaultController();
        $dc->clearSessionMessages();            // Clear the Session messages
    }*/
    
    public function checkArticleForm(string $route) {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
        
        if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['publish_date']) && isset($_POST['level']) && isset($_POST['csrf_token']) && isset($_POST['description'])) {
            
            $data = [
                'title'  => ucfirst(trim($_POST['title'])),                          // Removing unnecessary spaces and uppercasing the first letter of the firstname, the rest in lowercase.           
                'content' => strip_tags($_POST['content'], '<p><br><strong>'),       // Removing unnecessary spaces and lowering the email
                'publish_date'    => trim($_POST['publish_date']),                   // Removing unnecessary spaces in the id
                'level'  => $_POST['level'],
                'description'  => $_POST['description']
            ];
            
            if(empty($data['title']) || empty($data['content']) || empty($data['publish_date']) || empty($data['level']) || empty($data['description']) ){
                $_SESSION['error_message'] = "Tous les champs doivent être remplis";
                $this->redirect($route);
            }
            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {
            
                // TO DO : GESTION DE L'IMAGE
                
                if(strlen($data['title']) > 255){
                    $_SESSION['error_message'] = "Le titre ne peut pas faire plus de 255 caractères";
                    $this->redirect($route);
                }
                
                if(strlen($data['description']) > 255){
                    $_SESSION['error_message'] = "La description ne peut pas faire plus de 255 caractères";
                    $this->redirect($route);
                }
                
                return true;
            }
                    
            else {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect($route);
            }
        }
    }
}