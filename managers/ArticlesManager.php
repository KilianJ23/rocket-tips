<?php

class ArticlesManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    //Methos to create a new user
    
    /*public function createArticle(User $user): void
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
    }*/
    
    
    // Method to find an article by its level
    
     public function findArticleByLevel(int $level): ?Article {
        $query = $this->db->prepare('SELECT * FROM articles WHERE level=:level');

        $parameters = [
            "level" => $level
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $article = new Article($result["title"], $result["content"], $result["publish_date"], $result["level"], $result["description"]);

            return $article;
        }
        else {
            return null;
        }
    }
    
    public function getAll(): ?array {
        $query = $this->db->prepare('
            SELECT * 
            FROM articles');
            
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    // Methods to COUNT the number of articles per page
    
    public function getCountAll() {
        $query = $this->db->prepare("
            SELECT COUNT(*) 
            FROM articles");
            
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getCountAllByLevel(int $level) {
        $query = $this->db->prepare("
            SELECT COUNT(*) 
            FROM articles 
            WHERE level=:level");
        
        $query->bindValue(':level', $level, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }    
    
    // Methods to Fetch the articles per page
    
    public function getArticlesParPage(int $articles_par_page, int $offset): array {
        $query = $this->db->prepare("SELECT * FROM articles ORDER BY publish_date DESC LIMIT :limit OFFSET :offset");
        
        $query->bindValue(':limit', $articles_par_page, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getArticlesByPageAndLevel(int $articles_par_page, int $offset, int $level): array {
        $query = $this->db->prepare("
            SELECT * 
            FROM articles 
            WHERE level=:level ORDER BY publish_date DESC LIMIT :limit OFFSET :offset");
        
        $query->bindValue(':limit', $articles_par_page, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->bindValue(':level', $level, PDO::PARAM_INT);
        
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    // Method to fetch an article from his ID
    
    /*public function getArticleById(int $id): Article {
        $query = $this->db->prepare('
        SELECT * 
        FROM articles
        WHERE id=:id');

        $parameters = [
            "id" => $id
        ];

        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $article = new Article($result["title"], $result["content"], $result["publish_date"], $result["level"], $result["description"]);

            return $article;
        }
        else {
            return null;
        }
    }*/
    
    // Method to fetch an article 
    
    public function getArticleById(int $id): array {
        $query = $this->db->prepare("
            SELECT a.*, m.url as image_url, m.alt as image_alt
            FROM articles a
            LEFT JOIN medias m 
            ON m.owner_id = a.id AND m.type = 'article'
            WHERE a.id = :id
        ");
        
        $query->execute(['id' => $id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;

    }
    
    // Method to fetch the N last articles in the database
    
    public function getLastArticles(int $n): array {
    $query = $this->db->prepare("
        SELECT a.*, m.url as image_url, m.alt as image_alt
        FROM articles a
        LEFT JOIN medias m 
        ON m.owner_id = a.id AND m.type = 'article'
        ORDER BY a.publish_date DESC
        LIMIT :limit
    ");
    
    $query->bindValue(':limit', $n, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
