<?php

class ProductsController {
    private $product;
    private $transaction;
    private $session;
    public $redirectUrl;
    public $errors = [];

    public function __construct($product, $transaction, $session) {
        $this->product = $product;
        $this->transaction = $transaction;
        $this->session = $session;
        $this->redirectUrl = null;
    }

    /**
     * List all products
     */
    public function index() {
        // pagination
        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
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
            return $this->errorResponse("Product ID is required.");
        }

        $product = $this->product->getById($id);
        if (!$product) {
            return $this->errorResponse("Product not found.");
        }

        include __DIR__ . '/../views/products/view.php';
    }

    /**
     * Show new Product form
     */
    public function addProduct() {
        if (!$this->requireAdmin()) {
            header('Location: ' . $this->redirectUrl);
            return;
        }

        include __DIR__ . '/../views/products/add.php';
    }

    /**
     * Create a new product
     */
    public function createProduct() {
        if (!$this->requireAdmin()) {
            header('Location: ' . $this->redirectUrl);
            return;
        }

        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;

        // validation
        $this->validateProductData($name, $price, $quantity);

        if ($this->errors) {
            include __DIR__ . '/../views/products/add.php';
            return;
        }

        $this->product->create($name, $price, $quantity);
        $this->redirectUrl = '/products';
        header('Location: ' . $this->redirectUrl);
    }
    
    /**
     * Show update Product form
     */
    public function editProduct() {
        if (!$this->requireAdmin()) {
            header('Location: ' . $this->redirectUrl);
            return;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->errorResponse("Product ID is required.");
        }

        $product = $this->product->getById($id);
        if (!$product) {
            return $this->errorResponse("Product not found.");
        }

        include __DIR__ . '/../views/products/edit.php';
    }

    /**
     * Update a product
     */
    public function updateProduct() {
        if (!$this->requireAdmin()) {
            header('Location: ' . $this->redirectUrl);
            return;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->errorResponse("Product ID is required.");
        }

        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;

        // validation
        $this->validateProductData($name, $price, $quantity);

        if ($this->errors) {
            include __DIR__ . '/../views/products/edit.php';
            return;
        }

        $this->product->update($id, $name, $price, $quantity);
        $this->redirectUrl = '/products';
        header('Location: ' . $this->redirectUrl);
    }

    /**
     * Delete a product
     */
    public function deleteProduct() {
        if (!$this->requireAdmin()) {
            header('Location: ' . $this->redirectUrl);
            return;
        }

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
            $this->errors[] =  "Invalid product or quantity.";
        }

        $product = $this->product->getById($id);
        if (!$product) {
            $this->errors[] = "Product not found.";
        }

        if ($quantity > $product['quantity_available']) {
            $this->errors[] = "Insufficient stock for this product.";
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

        // header("Location: /products/view?id={$id}");
        
        $this->redirectUrl = "/products/view?id={$id}";
        header('Location: ' . $this->redirectUrl);
    }

    private function validateProductData($name, $price, $quantity) {
        if (empty($name)) {
            $this->errors[] = "Product name is required.";
        }
        if ($price <= 0) {
            $this->errors[] = "Price must be a positive number.";
        }
        if ($quantity < 0) {
            $this->errors[] = "Quantity Available cannot be negative.";
        }
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
        if (!isset($this->session['user_id'])) {
            $this->redirectUrl = '/auth/login';
            return false;
        }
        if (!$this->isAdmin()) {
            $this->redirectUrl = '/products';
            return false;
        }
        return true;
    }

    private function logTransaction($productId, $quantity, $price) {
        $userId = $this->session['user_id'];
        $totalPrice = $quantity * $price;
        $this->transaction->logPurchase($userId, $productId, $quantity, $totalPrice);
    }
}
?>
