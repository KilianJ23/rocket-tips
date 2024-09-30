<?php

class AuthController extends AbstractController
{
    private UserManager $um;

    public function __construct() {
        parent::__construct();
        $this->um = new UserManager();
    }

    //Méthode de displayRegister() : Affiche le template de register et vide les erreurs stockées en session.

    public function displayRegister() : void
    {
        $this->render('front/register.html.twig', [], []);
        
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
    }
    
    function isValidEmail($email) {
    // Utilise filter_var pour valider l'email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true; // L'email est valide
    } else {
        return false; // L'email n'est pas valide
    }
}
    
    public function validatePassword($password, $nbr = 8) {
        // The preg_match() function searches for a match between the regex pattern and the password.
        // The regex pattern is '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/'
        // This pattern requires:
        // - At least one uppercase letter (?=.*?[A-Z])
        // - At least one lowercase letter (?=.*?[a-z])
        // - At least one digit (?=.*?[0-9])
        // - At least one special character among #?!@$%^&*- (?=.*?[#?!@$%^&*-])
        // - A minimum length of 8 characters .{'.$nbr.',}
        // The function returns 1 if a match is found, otherwise 0.
        return preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $password);
    }

    public function checkRegister() : void
    {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
        
        
        // vérifier que tous les champs du formulaire sont là
        if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['csrf_token'])) {

            $tm = new CSRFTokenManager();

            if($tm->validateCSRFToken($_POST['csrf_token'])) {

                $user = $this->um->findUserByEmail($_POST['email']);
                

                if($user === null)
                {
                    if($_POST['password'] === $_POST['confirm_password'])
                    {
                        if($this->validatePassword($_POST['password']))
                        {
                        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        $user = new User($_POST['email'], $_POST['name'], $password, "USER");
                        
                            try {
                            $this->um->createUser($user);
                            $_SESSION['success_message'] = "Votre compte a bien été créé";
                            $this->redirect('connexion');
                                }
                            catch(\Exception $e)
                            {
                            $_SESSION['error_message'] = $e->getMessage();
                            $this->redirect('inscription');
                            }
                        }
                        else {
                            $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial (*, +, & ...).";
                            $this->redirect('inscription');
                        }
                    }
                    else
                    {
                        $_SESSION['error_message'] = "Les mots de passe ne correspondent pas.";
                        $this->redirect('inscription');
                    }
                }
                else
                {
                    $_SESSION['error_message'] = "Un compte existe déjà avec cette adresse.";
                    $this->redirect('inscription');
                }
            }
            else
            {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect('inscription');
            }
        }
        else
        {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            $this->redirect('inscription');
        }
    }
    
    //Méthode de login() : Affiche le template de login et vide les erreurs stockées en session.
    
    public function login() : void
    {
        $this->render('front/login.html.twig', [], []);
        
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }
    }

    //Méthode de checkLogin() : vérifie la présence de l'utilisateur dans la BDD et la cohérence des données entrées.

    public function checkLogin() : void
    {
        if(isset($_SESSION['error_message'])) {
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            unset($_SESSION['success_message']);
        }

        if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['csrf_token'])) {

            $tm = new CSRFTokenManager();
            

            if($tm->validateCSRFToken($_POST['csrf_token'])) {

                $user = $this->um->findUserByEmail($_POST['email']);
                
                if($user !== null)
                { 
                    if(password_verify($_POST['password'], $user->getPassword()))
                    {
                        $_SESSION['user'] = $user;
                        $_SESSION['role'] = $user->getRole();
                        $this->redirect('home');
                    }
                    else
                    {
                        $_SESSION['error_message'] = "Identifiant ou mot de passe incorrect.";
                        $this->redirect('connexion');
                    }
                }
                else
                {
                    $_SESSION['error_message'] = "Cet utilisateur n'existe pas";
                    $this->redirect('connexion');
                }
            }
            else
            {
                $_SESSION['error_message'] = "Le jeton CSRF est invalide.";
                $this->redirect('connexion');
            }
        }
        else
        {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            $this->redirect('connexion');
        }
    }

    public function logout() : void
    {
        session_destroy();
        $this->redirect('home');
    }
}