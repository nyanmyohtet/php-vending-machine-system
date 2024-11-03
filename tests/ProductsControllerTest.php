<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../controllers/ProductsController.php';

class ProductsControllerTest extends TestCase {
    private $productMock;
    private $transactionMock;
    private $sessionMock;
    private $controller;

    protected function setUp(): void {
        $this->productMock = $this->createMock(Product::class);
        $this->transactionMock = $this->createMock(Transaction::class);
        $this->sessionMock = [];
        $this->controller = new ProductsController($this->productMock, $this->sessionMock);
    }

    public function testIndexLoadsAllProducts() {
        $this->productMock->expects($this->once())
                          ->method('getAll')
                          ->willReturn([
                              ['id' => 1, 'name' => 'Product 1', 'price' => 10, 'quantity_available' => 5]
                          ]);

        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        $this->assertStringContainsString('Product 1', $output);
    }

    public function testViewProductWithoutIdReturnsError() {
        $_GET['id'] = null;

        ob_start();
        $this->controller->viewProduct();
        $output = ob_get_clean();

        $this->assertStringContainsString('Error: Product ID is required.', $output);
    }

    public function testCreateProductWithValidData() {
        $this->sessionMock['user_role'] = 'Admin';

        $_POST = [
            'name' => 'New Product',
            'price' => '9.99',
            'quantity' => '10'
        ];

        $this->productMock->expects($this->once())
                          ->method('create')
                          ->with('New Product', '9.99', '10')
                          ->willReturn(true);

        ob_start();
        $this->controller->createProduct();
        ob_end_clean();
    }

    public function testPurchaseProductOutOfStock() {
        $session = ['user_id' => 1];
        $controller = new ProductsController($this->productMock, $session);
    
        // Mock the product to have zero stock
        $this->productMock->method('getById')->willReturn(['quantity_available' => 0]);
    
        ob_start();
        $controller->purchaseProduct(1, 'SampleProduct');
        $output = ob_get_clean();
    
        $this->assertStringContainsString('Error: Sorry, this product is out of stock.', $output);
    }
}
?>
