<?php  

require_once 'Database.php';
require_once 'User.php';
/**
 * Class for handling Article-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Article extends Database {
    /**
     * Creates a new article.
     * @param string $title The article title.
     * @param string $content The article content.
     * @param int $author_id The ID of the author.
     * @param string|null $image_path The image file path (optional).
     * @return int The ID of the newly created article.
     */
    public function createArticle($title, $content, $author_id, $imagePath = null, $category_id = null) {
        $sql = "INSERT INTO articles (title, content, author_id, image_path, category_id, is_active) VALUES (?, ?, ?, ?, ?, 0)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $imagePath, $category_id]);
    }


    /**
     * Retrieves articles from the database.
     * @param int|null $id The article ID to retrieve, or null for all articles.
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT * FROM articles WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT articles.*, school_publication_users.username 
                FROM articles 
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id 
                ORDER BY articles.created_at DESC";

        return $this->executeQuery($sql);
    }

    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username, c.name AS category_name
                    FROM articles a
                    JOIN school_publication_users u ON a.author_id = u.user_id
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    WHERE a.article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }

        $sql = "SELECT a.*, u.username, u.is_admin, c.name AS category_name
                FROM articles a
                JOIN school_publication_users u ON a.author_id = u.user_id
                LEFT JOIN categories c ON a.category_id = c.category_id
                WHERE a.is_active = 1
                ORDER BY a.created_at DESC";

        return $this->executeQuery($sql);
    }

    public function getArticlesByUserID($user_id) {
        $sql = "SELECT articles.*, school_publication_users.username, categories.name AS category_name
                FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                LEFT JOIN categories ON articles.category_id = categories.category_id
                WHERE articles.author_id = ?
                ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }


    /**
     * Updates an article.
     * @param int $id The article ID to update.
     * @param string $title The new title.
     * @param string $content The new content.
     * @return int The number of affected rows.
     */
    public function updateArticle($id, $title, $content, $imagePath = null, $category_id = null) {
        $params = [$title, $content];
        $sql = "UPDATE articles SET title = ?, content = ?";

        if ($imagePath) {
            $sql .= ", image_path = ?";
            $params[] = $imagePath;
        }

        if ($category_id) {
            $sql .= ", category_id = ?";
            $params[] = $category_id;
        }

        $sql .= " WHERE article_id = ?";
        $params[] = $id;

        return $this->executeNonQuery($sql, $params);
    }


    /**
     * Toggles the visibility (is_active status) of an article.
     * This operation is restricted to admin users only.
     * @param int $id The article ID to update.
     * @param bool $is_active The new visibility status.
     * @return int The number of affected rows.
     */
    public function updateArticleVisibility($id, $is_active) {
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $id]);
    }


    /**
     * Deletes an article.
     * @param int $id The article ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    public function updateArticleCategory($article_id, $category_id) {
        $sql = "UPDATE articles SET category_id = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$category_id, $article_id]);
    }

}
?>