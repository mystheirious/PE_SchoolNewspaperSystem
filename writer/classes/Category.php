<?php
require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Category extends Database {

    /**
     * Creates a new category.
     * @param string $name The category name.
     * @return int The ID of the newly created category.
     */
    public function createCategory($name) {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $this->executeNonQuery($sql, [$name]);
        return $this->lastInsertId();
    }

    /**
     * Retrieves categories from the database.
     * @param int|null $id The category ID to retrieve, or null for all categories.
     * @return array|mixed
     */
    public function getCategories($id = null) {
        if ($id) {
            $sql = "SELECT * FROM categories WHERE category_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT * FROM categories ORDER BY created_at DESC";
        return $this->executeQuery($sql);
    }

    /**
     * Updates a category name.
     * @param int $id The category ID to update.
     * @param string $name The new category name.
     * @return int The number of affected rows.
     */
    public function updateCategory($id, $name) {
        $sql = "UPDATE categories SET name = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$name, $id]);
    }

    /**
     * Deletes a category.
     * @param int $id The category ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }
}
?>