<?php

class Transaction {
    private $conn;
    private $table = 'transactions';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function logPurchase($userId, $productId, $quantity, $totalPrice) {
        $query = "INSERT INTO " . $this->table . " (user_id, product_id, quantity, total_price, purchase_date) 
                  VALUES (:user_id, :product_id, :quantity, :total_price, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':total_price', $totalPrice);
        $stmt->execute();
    }
}
?>