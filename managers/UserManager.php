<?php

class UserManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    // Method to create a new user
    
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
    
    public function updateUser(User $user): void
    {
        $query = $this->db->prepare('UPDATE users SET email = :email, name = :name,  role = :role WHERE id = :id');
            
            $parameters = [
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'role' => $user->getRole(),
                'id' => $user->getId()
                ];
        $query->execute($parameters);
    }
    
    // Method to Delete a user
    
    public function deleteUser(int $id): void
    {
        $query = $this->db->prepare('DELETE FROM users WHERE id = :id');
        
        $parameters = [
            'id' => $id
            ];
        
        $query->execute($parameters);
        $address = $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    // Method to find a user from his Email - returns a user
    
     public function findUserByEmail(string $email): ?User
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email=:email');

        $parameters = [
            "email" => $email
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User($result["email"], $result["name"], $result["password"], $result["role"]);
            $user->setId($result["id"]);

            return $user;
        }
        else {
            return null;
        }
    }
    
    // Method to find a user from his Name - returns a user
    
     public function findUserByName(string $name): ?User
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE name=:name');

        $parameters = [
            "name" => $name
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
    
    // Method to return the Role of a user
    
     public function findRole(string $email): string
    {
        $query = $this->db->prepare('SELECT role FROM users WHERE email=:email');

        $parameters = [
            "email" => $email
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);
    }
    
    // Method to return all users
    
    public function getAllUsers(): array 
    {
        $users = [];
        
        $query = $this->db->prepare("SELECT * FROM users");
        
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    // Method to return the information of a single user from his ID, in an array
    
    public function getUserById(int $id): array
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE id=:id');

        $parameters = [
            "id" => $id
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return $result;
    }
    
    public function changePassword(User $user): void {
                
        $sql = "UPDATE users SET password = :password WHERE id = :id";
 
        $parameters = [
            'password'  => $user->getPassword(),
            'id' => $user->getId()
        ];

        $this->execute($sql, $parameters);
    }
}