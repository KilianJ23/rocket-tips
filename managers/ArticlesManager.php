<?php

class ArticlesManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }
    
    //Method to create a new Article
    
    public function createArticle(Article $article): Article
    {
        $query = $this->db->prepare('INSERT INTO articles (title, content, publish_date, level, description) VALUES (:title, :content, :publish_date, :level, :description)');
        
        $parameters = [
            "title" => $article->getTitle(),
            "content" => $article->getContent(),
            "level" => $article->getLevel(),
            "description" => $article->getDescription(),
            "publish_date" => $article->getPublishDate()
        ];
        
        $query->execute($parameters);

        $article->setId($this->db->lastInsertId());
        return $article;
    }
    
    //Method to update an Article
    
    public function updateArticle(Article $article): bool
    {
        $query = $this->db->prepare('
            UPDATE articles 
            SET title = :title, content = :content, publish_date = :publish_date, level = :level, description = :description
            WHERE id = :id
        ');
        
        $parameters = [
            "title" => $article->getTitle(),
            "content" => $article->getContent(),
            "level" => $article->getLevel(),
            "description" => $article->getDescription(),
            "publish_date" => $article->getPublishDate(),
            "id" => $article->getId()
        ];
        
        return $query->execute($parameters);
    }
    
    // Method to delete an Article and its associated media
    
    public function deleteArticleAndMedia(int $article_id, string $type): void
    {
        try {
            // Start a transaction to ensure all operations are performed together
            $this->db->beginTransaction();
    
            // 1. Delete the media associated with the article in the 'medias' table
            $deleteMediaQuery = $this->db->prepare('DELETE FROM medias WHERE owner_id = :article_id AND type = :type');
            
            $deleteMediaQuery->bindValue(':article_id', $article_id, PDO::PARAM_INT);
            $deleteMediaQuery->bindValue(':type', $type, PDO::PARAM_STR); // Binding the type properly
            
            $deleteMediaQuery->execute();
    
            // 2. Delete the article in the 'articles' table
            $deleteArticleQuery = $this->db->prepare('DELETE FROM articles WHERE id = :article_id');
            $deleteArticleQuery->bindValue(':article_id', $article_id, PDO::PARAM_INT);
            $deleteArticleQuery->execute();
    
            // If all operations succeed, commit the transaction
            $this->db->commit();
        } catch (Exception $e) {
            // In case of error, roll back the transaction
            $this->db->rollBack();
            throw new Exception("Error deleting article and its media: " . $e->getMessage());
        }
    }


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
    
    // Method to COUNT the TOTAL number of Medias
    
    public function getCountAll() {
        $query = $this->db->prepare("
            SELECT COUNT(*) 
            FROM articles");
            
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    // Method to COUNT the number of articles of a level in a particuler
    
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
    
    // Method to fetch an article and his media
    
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
