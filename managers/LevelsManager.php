<?php

class LevelsManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    // Method to Fetch the name of the difficulty level
    
     public function getLevelName(int $id) {
        $query = $this->db->prepare('SELECT level FROM articles_levels WHERE id=:id');

        $parameters = [
            "id" => $id
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['level'] ?? 'inexistant';
    }
}