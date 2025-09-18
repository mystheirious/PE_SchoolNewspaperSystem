<?php
require_once 'Database.php';

class Notification extends Database {

    public function createNotification($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$user_id, $message]);
    }

    public function getNotificationsByUser($user_id) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    public function markAllAsRead($user_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
        return $this->executeNonQuery($sql, [$user_id]);
    }
}
?>