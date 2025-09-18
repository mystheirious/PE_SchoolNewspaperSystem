<?php
require_once 'Database.php';

class EditRequest extends Database {

    public function createRequest($article_id, $requester_id) {
        $sql = "INSERT INTO edit_requests (article_id, requester_id) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $requester_id]);
    }

    public function updateStatus($request_id, $status) {
        $sql = "UPDATE edit_requests SET status = ? WHERE request_id = ?";
        return $this->executeNonQuery($sql, [$status, $request_id]);
    }

    public function getRequestsForAuthor($author_id) {
        $sql = "SELECT er.*, a.title, u.username AS requester_name
                FROM edit_requests er
                JOIN articles a ON er.article_id = a.article_id
                JOIN school_publication_users u ON er.requester_id = u.user_id
                WHERE a.author_id = ?
                ORDER BY er.created_at DESC";
        return $this->executeQuery($sql, [$author_id]);
    }

    public function getExistingRequest($article_id, $requester_id) {
        $sql = "SELECT * FROM edit_requests 
                WHERE article_id = ? AND requester_id = ? 
                AND status IN ('pending','approved')";
        return $this->executeQuerySingle($sql, [$article_id, $requester_id]);
    }

    public function getRequestById($request_id) {
        $sql = "SELECT * FROM edit_requests WHERE request_id = ?";
        return $this->executeQuerySingle($sql, [$request_id]);
    }

    public function getRequestsByRequester($requester_id) {
        $sql = "SELECT er.*, a.title, u.username AS author_name
                FROM edit_requests er
                JOIN articles a ON er.article_id = a.article_id
                JOIN school_publication_users u ON a.author_id = u.user_id
                WHERE er.requester_id = ?
                ORDER BY er.created_at DESC";
        return $this->executeQuery($sql, [$requester_id]);
    }
}
?>