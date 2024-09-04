<?php

class UserManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    //Methos to create a new user
    
    public function createUser(User $user): void
    {

        $parameters = [
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "password" => $user->getPassword(),
            "role" => $user->getRole(),

        ];
        
        

        $query = $this->db->prepare('INSERT INTO users (email, name, password, role) VALUES (:email, :name, :password, :role)');
        

        $query->execute($parameters);

        $user->setId($this->db->lastInsertId());
    }
    
    
    //Method to find a user
    
     public function findUserByEmail(string $email): ?User
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email=:email');

        $parameters = [
            "email" => $email
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User($result["name"], $result["email"], $result["password"], $result["role"]);
            $user->setId($result["id"]);

            return $user;
        }
        else {
            return null;
        }
    }
}