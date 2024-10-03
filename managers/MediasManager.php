<?php

class MediasManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    public function addMedia(string $url, string $alt, int $owner_id, string $owner_type): bool {
    // Vérifier que owner_id existe dans la bonne table en fonction de owner_type
        if ($owner_type === 'article') {
            $query = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE id = :id");
        } elseif ($owner_type === 'user') {
            $query = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :id");
        }
    
        $query->bindValue(':id', $owner_id, PDO::PARAM_INT);
        $query->execute();
        
        // Vérifier que l'ID existe
        if ($query->fetchColumn() == 0) {
            throw new Exception("L'ID $owner_id n'existe pas dans la table $owner_type");
        }
    
        // Insérer le média si tout est correct
        $query = $this->db->prepare("
            INSERT INTO medias (url, alt, owner_id, owner_type) 
            VALUES (:url, :alt, :owner_id, :owner_type)
        ");
        $query->bindValue(':url', $url, PDO::PARAM_STR);
        $query->bindValue(':alt', $alt, PDO::PARAM_STR);
        $query->bindValue(':owner_id', $owner_id, PDO::PARAM_INT);
        $query->bindValue(':owner_type', $owner_type, PDO::PARAM_STR);
        
        return $query->execute();
    }

}