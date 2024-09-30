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
        $um = new UserManager();
        $users = $this->um->getAllUsers();
        
        $this->render('front/admin/users/users.html.twig', [
            "users" => $users], []);
    }
    
    // Method displayUser() : Shows the information of a single user
    
    public function displayUser() 
    {
        $userId = $_GET['id'];
        $user = $this->um->getUserById($userId);
        
        $this->render('front/admin/users/modifyUser.html.twig', [
            'user'      => $user
        ], []);
        
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
    }
}