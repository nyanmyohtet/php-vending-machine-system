<?php

class ProductsController {
    private $product;
    private $transaction;
    private $session;

    public function __construct($product, $transaction, $session) {
        $this->product = $product;
        $this->transaction = $transaction;
        $this->session = $session;
    }

    /**
     * List all products
     */
    public function index() {
        // pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 2;
        $offset = ($page - 1) * $limit;

        // sorting
        $sortField = $_GET['sort'] ?? 'name';
        $sortOrder = $_GET['order'] ?? 'asc'; 

        $products = $this->product->getAll($limit, $offset, $sortField, $sortOrder);
        $totalProducts = $this->product->getTotalCount();

        $totalPages = ceil($totalProducts / $limit);

        include __DIR__ . '/../views/products/list.php';
    }

    /**
     * View a single product
     */
    public function viewProduct() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $errors[] = "Product ID is required.";
            include __DIR__ . '/../views/products/view.php';
            return;
        }

        $product = $this->product->getById($id);
        include __DIR__ . '/../views/products/view.php';
    }

    /**
     * Show new Product form
     */
    public function addProduct() {
        $this->requireAdmin();

        include __DIR__ . '/../views/products/add.php';
    }

    /**
     * Create a new product
     */
    public function createProduct() {
        $this->requireAdmin();

        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $errors = [];

        // validation
        if (empty($name)) {
            $errors[] = "Product name is required.";
        }
        if ($price === null || $price <= 0) {
            $errors[] = "Price must be a positive number.";
        }
        if ($quantity === null || $quantity < 0) {
            $errors[] = "Quantity Available cannot be negative.";
        }

        if ($errors) {
            include __DIR__ . '/../views/products/add.php';
            return;
        }

        $this->product->create($name, $price, $quantity);
        header('Location: /products');
    }
    
    /**
     * Show update Product form
     */
    public function editProduct() {
        $this->requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->errorResponse("Product ID is required.");
        }

        $product = $this->product->getById($id);
        include __DIR__ . '/../views/products/edit.php';
    }

    /**
     * Update a product
     */
    public function updateProduct() {
        $this->requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->errorResponse("Product ID is required.");
        }

        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $errors = [];

        // validation
        if (empty($name)) {
            $errors[] = "Product name is required.";
        }
        if ($price === null || $price <= 0) {
            $errors[] = "Price must be a positive number.";
        }
        if ($quantity === null || $quantity < 0) {
            $errors[] = "Quantity Available cannot be negative.";
        }

        if ($errors) {
            include __DIR__ . '/../views/products/edit.php';
            return;
        }

        $this->product->update($id, $name, $price, $quantity);
        header('Location: /products');
    }

    /**
     * Delete a product
     */
    public function deleteProduct() {
        $this->requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->errorResponse("Product ID is required.");
        }

        $this->product->delete($id);
        header('Location: /products');
    }

    /**
     * Show purchase product form
     */
    public function purchaseProduct($id, $productName) {
        $this->requireLogin();
        $this->requireUser();
            
        $product = $this->product->getById($id);
        if (!$product) {
            return $this->errorResponse("Product not found.");
        }
    
        include __DIR__ . '/../views/products/purchase.php';
    }

    /**
     * Purchase a Product
     */
    public function processPurchase() {
        $this->requireLogin();
        $this->requireUser();

        $id = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 0;
        $errors = [];

        if (!$id || $quantity <= 0) {
            return $this->errorResponse("Invalid product or quantity.");
        }

        $product = $this->product->getById($id);
        if (!$product) {
            return $this->errorResponse("Product not found.");
        }

        if ($quantity > $product['quantity_available']) {
            $errors[] = "Insufficient stock for this product.";
        }

        if ($errors) {
            include __DIR__ . '/../views/products/purchase.php';
            return;
        }

        // Update product quantity
        $newQuantity = $product['quantity_available'] - $quantity;
        $this->product->updateQuantity($id, $newQuantity);

        // Log the transaction
        $this->logTransaction($id, $quantity, $product['price']);

        header("Location: /products/view?id={$id}");
    }

    private function errorResponse($message) {
        echo 'Error: ' . $message;
        return;
    }
    
    // helper method to check authorization
    private function isAdmin() {
        return isset($this->session['user_role']) && $this->session['user_role'] === 'Admin';
    }

    // helper methods for unauthorized access
    private function requireLogin() {
        if (!isset($this->session['user_id'])) {
            header('Location: /auth/login');
            return;
        }
    }

    private function requireUser() {
        if ($this->isAdmin()) {
            header('Location: /products');
            return;
        }
    }

    private function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: /products');
            return;
        }
    }

    private function logTransaction($productId, $quantity, $price) {
        $userId = $this->session['user_id'];
        $totalPrice = $quantity * $price;
        $this->transaction->logPurchase($userId, $productId, $quantity, $totalPrice);
    }
}
?>
