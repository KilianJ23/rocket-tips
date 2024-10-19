<?php

class MediasManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    public function addMedia(string $url, string $alt, int $owner_id, string $owner_type, string $publish_date): bool {
    
    // Check that the owner_id exists in the owner_type table
        if ($owner_type === 'article') {
            $query = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE id = :id");
        } elseif ($owner_type === 'user') {
            $query = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :id");
        }
    
        $query->bindValue(':id', $owner_id, PDO::PARAM_INT);
        $query->execute();
        
        // Check that the owner_id exists
        if ($query->fetchColumn() == 0) {
            throw new Exception("L'ID $owner_id n'existe pas dans la table $owner_type");
        }
    
        // If everything is fine : insert the media
        $query = $this->db->prepare("
            INSERT INTO medias (url, alt, owner_id, type, created_at) 
            VALUES (:url, :alt, :owner_id, :owner_type, :publish_date)
        ");
        $query->bindValue(':url', $url, PDO::PARAM_STR);
        $query->bindValue(':alt', $alt, PDO::PARAM_STR);
        $query->bindValue(':owner_id', $owner_id, PDO::PARAM_INT);
        $query->bindValue(':owner_type', $owner_type, PDO::PARAM_STR);
        $query->bindValue(':publish_date', $publish_date, PDO::PARAM_STR);
        
        return $query->execute();
        
    }
    
    // Methods to UPDATE an existing media
    
    public function updateMedia(int $media_id, string $url, string $alt, int $owner_id, string $owner_type, string $publish_date): bool {
    
        // Checking that owner_id exists in the right owner_type table
        if ($owner_type === 'article') {
            $query = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE id = :id");
        } elseif ($owner_type === 'user') {
            $query = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :id");
        }
        
        $query->bindValue(':id', $owner_id, PDO::PARAM_INT);
        $query->execute();
        
        // Checking that owner_id exists
        if ($query->fetchColumn() == 0) {
            throw new Exception("L'ID $owner_id n'existe pas dans la table $owner_type");
        }
        
        // If all is good : update the media
        $query = $this->db->prepare("
            UPDATE medias 
            SET url = :url, alt = :alt, owner_id = :owner_id, type = :owner_type, created_at = :publish_date
            WHERE id = :media_id
        ");
        
        $query->bindValue(':url', $url, PDO::PARAM_STR);
        $query->bindValue(':alt', $alt, PDO::PARAM_STR);
        $query->bindValue(':owner_id', $owner_id, PDO::PARAM_INT);
        $query->bindValue(':owner_type', $owner_type, PDO::PARAM_STR);
        $query->bindValue(':publish_date', $publish_date, PDO::PARAM_STR);
        $query->bindValue(':media_id', $media_id, PDO::PARAM_INT);
        
        return $query->execute();
    }
    
    // METHOD to UPDATE the MEDIA alt
    
    public function updateMediaAlt(int $id, string $alt): void {
        
        $query = $this->db->prepare("
            UPDATE medias 
            SET alt = :alt
            WHERE id = :id
        ");
        
        $parameters = [
            "alt" => $alt,
            "id" => $id
        ];

        $query->execute($parameters);
        
    }

    
    // Method to COUNT the TOTAL number of Medias
    
    public function getCountAll() {
        $query = $this->db->prepare("
            SELECT COUNT(*) 
            FROM medias");
            
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    // Method to get the ID of a Media from its article
    
    public function getIdFromOwner(int $owner_id, string $owner_type): ?int {

        $query = $this->db->prepare('SELECT id FROM medias WHERE owner_id = :owner_id AND type = :owner_type LIMIT 1');
        
        $query->bindValue(':owner_id', $owner_id, PDO::PARAM_INT);
        $query->bindValue(':owner_type', $owner_type, PDO::PARAM_STR);
        
        $query->execute();
        
        // Récupérer l'ID du média
        $result = $query->fetchColumn();
        
        // Vérifier si un média a été trouvé
        if ($result === false) {
            return null;  // Retourne null si aucun média n'a été trouvé
        }
        
        return $result;
    }

}