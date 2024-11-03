<?php
class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all products
     */
    public function getAll($limit, $offset, $sortField, $sortOrder) {
        $allowedFields = ['name', 'price', 'quantity_available'];
        $allowedOrder = ['asc', 'desc'];

        // Only valid fields and order are used
        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'name';
        }
        if (!in_array($sortOrder, $allowedOrder)) {
            $sortOrder = 'asc';
        }

        $query = "SELECT * FROM " . $this->table . " ORDER BY {$sortField} {$sortOrder} LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total product count for pagination
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'];
    }

    /**
     * Retrieve a single product by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new product
     */
    public function create($name, $price, $quantity) {
        $sql = "INSERT INTO " . $this->table . " (name, price, quantity_available) VALUES (:name, :price, :quantity)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }
    
    /**
     * Update a product by ID
     */
    public function update($id, $name, $price, $quantity) {
        $sql = "UPDATE " . $this->table . " SET name = :name, price = :price, quantity_available = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    /**
     * Update a product quantity
     */
    public function updateQuantity($id, $newQuantity) {
        $sql = "UPDATE " . $this->table . " SET quantity_available = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete a product by ID
     */
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
