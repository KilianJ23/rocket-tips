<?php

class CommentsManager extends AbstractManager {

    public function __construct() {
        // Call to the AbstractManager constructor so that it initiates the connection with DB 
        parent::__construct();
    }

    // Method to get all comments from an article ID

    public function getCommentsByArticleId(int $articleId): array {

        $query = $this->db->prepare('
            SELECT comments.content, comments.created_at, comments.id, users.name
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
    
    // Method to add a comment in the DB
    
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
    
    // Method to delete a comment from the DB
    
    public function deleteComment(int $commentId): bool {
        $query = $this->db->prepare('DELETE FROM comments WHERE id = :id');
        
        $parameters = [
            'id' => $commentId
            ];
        
        return $query->execute($parameters);
    }
}
