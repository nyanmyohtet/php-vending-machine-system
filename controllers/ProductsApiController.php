<?php

class ProductsApiController {
    private $product;
    private $middleware;

    public function __construct($product, $tokenMiddleware) {
        $this->product = $product;
        $this->middleware = $tokenMiddleware;
    }

    public function getProducts() {
        $this->middleware->verifyToken();

        $products = $this->product->getAll();
        return $this->jsonResponse($products);
    }

    public function getProductDetails($id) {
        $this->middleware->verifyToken();

        $product = $this->product->getById($id);
        if (!$product) {
            return $this->jsonResponse(['error' => 'Product not found'], 404);
        }
        
        return $this->jsonResponse($product);
    }

    public function addProduct() {
        $this->middleware->verifyToken();

        $input = json_decode(file_get_contents('php://input'), true);
        if ($this->product->create($input['name'], $input['price'], $input['quantity'])) {
            return $this->jsonResponse(['message' => 'Product created successfully'], 201);
        } else {
            return $this->jsonResponse(['error' => 'Failed to create product'], 500);
        }
    }

    public function updateProduct($id) {
        $this->middleware->verifyToken();

        $product = $this->product->getById($id);
        if (!$product) {
            return $this->jsonResponse(['error' => 'Product not found'], 404);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if ($this->product->update($id, $input['name'], $input['price'], $input['quantity'])) {
            return $this->jsonResponse(['message' => 'Product updated successfully']);
        } else {
            return $this->jsonResponse(['error' => 'Failed to update product'], 500);
        }
    }

    public function deleteProduct($id) {
        $this->middleware->verifyToken();

        $product = $this->product->getById($id);
        if (!$product) {
            return $this->jsonResponse(['error' => 'Product not found'], 404);
        }

        if ($this->product->delete($id)) {
            return $this->jsonResponse(['message' => 'Product deleted successfully']);
        } else {
            return $this->jsonResponse(['error' => 'Failed to delete product'], 500);
        }
    }

    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
