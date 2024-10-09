<?php

class CommentsManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }


    public function getCommentsByArticleId(int $articleId): array {

        $query = $this->db->prepare('
            SELECT comments.content, comments.created_at, users.name
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.article_id = :article_id
            ORDER BY comments.created_at DESC');
    
        $parameters = [
            'article_id' => $articleId
        ];
    
        $query->execute($parameters);
    
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addComment(int $articleId, string $commentContent, int $userId): void {
    
        $query = $this->db->prepare('
            INSERT INTO comments (article_id, user_id, content, created_at)
            VALUES (:article_id, :user_id, :content, NOW())
        ');

        $parameters = [
            'article_id' => $articleId,
            'user_id' => $userId,
            'content' => $commentContent
        ];
        
        $query->execute($parameters);
    }
}
