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
        $this->sessionMockMock = [
            'user_id' => 1,
            'user_role' => 'User',
        ];
        $this->controller = new ProductsController($this->productMock, $this->transactionMock, $this->sessionMockMock);
    }

    protected function tearDown(): void {
        $_GET = [];
    }

    public function testIndexPagination() {
        // Arrange
        $_GET['page'] = 2;
        $_GET['limit'] = 2;

        $mockedProducts = [
            ['id' => 3, 'name' => 'Product C'],
            ['id' => 4, 'name' => 'Product D']
        ];
        $this->productMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo(2), $this->equalTo(2))
            ->willReturn($mockedProducts);

        $this->productMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(4);

        // Capture output
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        // Assert
        $this->assertStringContainsString('Product C', $output);
        $this->assertStringContainsString('Product D', $output);
    }

    public function testIndexSorting() {
        // Arrange
        $_GET['sort'] = 'price';
        $_GET['order'] = 'desc';

        $mockedProducts = [
            ['id' => 1, 'name' => 'Product A', 'price' => 200],
            ['id' => 2, 'name' => 'Product B', 'price' => 100]
        ];

        $this->productMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->equalTo('price'),
                $this->equalTo('desc')
            )
            ->willReturn($mockedProducts);

        $this->productMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(2);

        // Capture output
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        // Assert
        $this->assertStringContainsString('Product A', $output);
        $this->assertStringContainsString('Product B', $output);
    }

    public function testIndexDefaultParameters() {
        // Arrange: No pagination or sorting parameters set
        $mockedProducts = [
            ['id' => 1, 'name' => 'Product A'],
            ['id' => 2, 'name' => 'Product B'],
            ['id' => 3, 'name' => 'Product C'],
            ['id' => 4, 'name' => 'Product C'],
            ['id' => 5, 'name' => 'Product D'],
            ['id' => 6, 'name' => 'Product E'],
            ['id' => 7, 'name' => 'Product F'],
            ['id' => 8, 'name' => 'Product G'],
            ['id' => 9, 'name' => 'Product H'],
            ['id' => 10, 'name' => 'Product I']
        ];

        $this->productMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->equalTo(10),    // Default limit
                $this->equalTo(0),    // Default offset (page 1)
                $this->equalTo('name'), // Default sort field
                $this->equalTo('asc') // Default sort order
            )
            ->willReturn($mockedProducts);

        $this->productMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(10);

        // Capture output
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        // Assert
        $this->assertStringContainsString('Product A', $output);
        $this->assertStringContainsString('Product B', $output);
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

    public function testViewProductWithValidId() {
        $_GET['id'] = 1;
        $expectedProduct = ['id' => 1, 'name' => 'Test Product', 'price' => 100, 'quantity_available' => 10];

        $this->productMock->expects($this->once())
            ->method('getById')
            ->with($this->equalTo(1))
            ->willReturn($expectedProduct);

        ob_start();
        $this->controller->viewProduct();
        $output = ob_get_clean();

        $this->assertStringContainsString("Test Product", $output, "Expected product details for valid product ID.");
        $this->assertStringContainsString("100", $output, "Expected product price for valid product ID.");
        $this->assertStringContainsString("10", $output, "Expected product quantity for valid product ID.");
    }

    public function testViewProductWithNonExistentId() {
        $_GET['id'] = 999;

        $this->productMock->expects($this->once())
            ->method('getById')
            ->with($this->equalTo(999))
            ->willReturn(null);

        ob_start();
        $this->controller->viewProduct();
        $output = ob_get_clean();

        $this->assertStringContainsString("Product not found.", $output, "Expected error message for non-existent product ID.");
    }

    // addProduct
    public function testAddProductAsAdmin() {
        // Set user role to Admin
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        ob_start();
        $this->controller->addProduct();
        $output = ob_get_clean();

        $this->assertStringContainsString("<form", $output, "Expected the add product form to be displayed for an admin user.");
        $this->assertStringContainsString("name=\"name\"", $output, "Expected 'name' input field in the form.");
        $this->assertStringContainsString("name=\"price\"", $output, "Expected 'price' input field in the form.");
        $this->assertStringContainsString("name=\"quantity\"", $output, "Expected 'quantity' input field in the form.");
    }

    public function testAddProductAsNonAdmin() {
        // user is not an Admin
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'User';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        ob_start();
        $this->controller->addProduct();
        $output = ob_get_clean();

        $this->assertEquals('/products', $this->controller->redirectUrl, "Expected redirection to /products for a non-admin user.");
    }

    public function testAddProductWithNoSession() {
        // Clear session to simulate no user logged in
        $this->controller = new ProductsController($this->productMock, null, []);

        ob_start();
        $this->controller->addProduct();
        $output = ob_get_clean();

        $this->assertEquals('/auth/login', $this->controller->redirectUrl, "Expected redirection to /auth/login for no active session.");
    }

    // createProduct
    public function testCreateProductAsAdmin()
    {
        
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Mock product creation
        $this->productMock->expects($this->once())
            ->method('create')
            ->with('Test Product', 100, 10);

        $_POST['name'] = 'Test Product';
        $_POST['price'] = 100;
        $_POST['quantity'] = 10;

        $this->controller->createProduct();

        // Verify redirection to products list after creation
        $this->assertEquals('/products', $this->controller->redirectUrl);
        $this->assertEmpty($this->controller->errors, "Expected no validation errors.");
    }

    public function testCreateProductWithMissingName()
    {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        $_POST['name'] = '';
        $_POST['price'] = 50;
        $_POST['quantity'] = 5;

        $this->controller->createProduct();

        $this->assertContains("Product name is required.", $this->controller->errors);
        $this->assertNull($this->controller->redirectUrl, "Expected no redirection due to validation errors.");
    }

    public function testCreateProductWithInvalidPrice()
    {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        $_POST['name'] = 'Valid Product';
        $_POST['price'] = -10; // Invalid price
        $_POST['quantity'] = 5;

        $this->controller->createProduct();

        $this->assertContains("Price must be a positive number.", $this->controller->errors);
        $this->assertNull($this->controller->redirectUrl, "Expected no redirection due to validation errors.");
    }

    public function testCreateProductWithNegativeQuantity()
    {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        $_POST['name'] = 'Valid Product';
        $_POST['price'] = 50;
        $_POST['quantity'] = -5; // Invalid quantity

        $this->controller->createProduct();

        $this->assertContains("Quantity Available cannot be negative.", $this->controller->errors);
        $this->assertNull($this->controller->redirectUrl, "Expected no redirection due to validation errors.");
    }

    public function testCreateProductWithNoSession() {
        // Clear session to simulate no user logged in
        $this->controller = new ProductsController($this->productMock, null, []);

        $this->controller->createProduct();

        $this->assertEquals('/auth/login', $this->controller->redirectUrl, "Expected redirection to /auth/login for no active session.");
    }

    public function testCreateProductWithValidData() {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Simulate form submission with valid product data
        $_POST['name'] = 'Test Product';
        $_POST['price'] = 100;
        $_POST['quantity'] = 10;

        $this->productMock->expects($this->once())
                    ->method('create')
                    ->with(
                        $this->equalTo('Test Product'),
                        $this->equalTo(100),
                        $this->equalTo(10)
                    );

        // Run the createProduct method
        $this->controller->createProduct();

        // Assert there are no errors
        $this->assertEmpty($this->controller->errors, "Expected no validation errors.");

        // Check that the redirect URL is set correctly
        $this->assertEquals('/products', $this->controller->redirectUrl, "Expected redirection to product listing page.");
    }

    // editProduct
    public function testEditProductWithMissingId() {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Remove any ID from GET parameters
        unset($_GET['id']);
        
        // Expect the error response method to be called with a specific error message
        $this->expectOutputString('Error: Product ID is required.');
        
        // Run the editProduct method
        $this->controller->editProduct();
    }

    public function testEditProductWithNonExistentId() {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Set a non-existent product ID
        $_GET['id'] = 999;
        
        // Mock getById to return null, simulating a product that does not exist
        $this->productMock->expects($this->once())
                    ->method('getById')
                    ->with($this->equalTo(999))
                    ->willReturn(null);
        
        // Expect the error response
        $this->expectOutputString('Error: Product not found.');
        
        // Run the editProduct method
        $this->controller->editProduct();
    }

    public function testEditProductWithValidId() {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Set a valid product ID
        $_GET['id'] = 1;
        
        // Mock the product data that would be returned by getById
        $productData = [
            'id' => 1,
            'name' => 'Sample Product',
            'price' => 100,
            'quantity' => 50
        ];
        
        // Set up the expectation for getById to return the mock product data
        $this->productMock->expects($this->once())
                    ->method('getById')
                    ->with($this->equalTo(1))
                    ->willReturn($productData);
        
        // Capture the output from including the edit view
        $this->expectOutputRegex('/Sample Product/');
        
        // Run the editProduct method
        $this->controller->editProduct();
    }

    // updateProduct
    public function testUpdateProductWithMissingId() {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Remove any ID from GET parameters
        unset($_GET['id']);
        
        // Expect the error response method to be called with a specific error message
        $this->expectOutputString('Error: Product ID is required.');
        
        // Run the updateProduct method
        $this->controller->updateProduct();
    }

    public function testUpdateProductWithMissingName()
    {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Set a valid product ID
        $_GET['id'] = 1;
        
        // Mock the product update method to not be called, as validation should fail
        $this->productMock->expects($this->never())->method('update');
        
        // Simulate missing `name` field
        $_POST['name'] = '';
        $_POST['price'] = 100;
        $_POST['quantity'] = 50;

        // Run the updateProduct method
        $this->controller->updateProduct();

        // Capture output from the view with the validation error
        $this->assertContains("Product name is required.", $this->controller->errors);
        $this->assertNull($this->controller->redirectUrl, "Expected no redirection due to validation errors.");
        
    }

    public function testUpdateProductWithInvalidPrice()
    {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Set a valid product ID
        $_GET['id'] = 1;

        // Mock the product update method to not be called, as validation should fail
        $this->productMock->expects($this->never())->method('update');
        
        // Simulate invalid `price` (zero or negative)
        $_POST['name'] = 'Updated Product';
        $_POST['price'] = -10; // Invalid price
        $_POST['quantity'] = 50;

        // Run the updateProduct method
        $this->controller->updateProduct();

        $this->assertContains("Price must be a positive number.", $this->controller->errors);
        $this->assertNull($this->controller->redirectUrl, "Expected no redirection due to validation errors.");
    }

    public function testUpdateProductWithNegativeQuantity()
    {
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Set a valid product ID
        $_GET['id'] = 1;

        // Mock the product update method to not be called, as validation should fail
        $this->productMock->expects($this->never())->method('update');
        
        // Simulate invalid `quantity` (negative)
        $_POST['name'] = 'Updated Product';
        $_POST['price'] = 100;
        $_POST['quantity'] = -5; // Invalid quantity

        // Run the updateProduct method
        $this->controller->updateProduct();

        // Capture output from the view with the validation error
        $this->assertContains("Quantity Available cannot be negative.", $this->controller->errors);
        $this->assertNull($this->controller->redirectUrl, "Expected no redirection due to validation errors.");
    }

    public function testUpdateProductWithValidData()
    {
        
        $this->sessionMock['user_id'] = 1;
        $this->sessionMock['user_role'] = 'Admin';
        $this->controller = new ProductsController($this->productMock, null, $this->sessionMock);

        // Set a valid product ID
        $_GET['id'] = 1;

        // Mock valid POST data for updating the product
        $_POST['name'] = 'Updated Product';
        $_POST['price'] = 150;
        $_POST['quantity'] = 10;

        // Expect the product update method to be called once with the new values
        $this->productMock->expects($this->once())
                    ->method('update')
                    ->with(
                        $this->equalTo(1),
                        $this->equalTo('Updated Product'),
                        $this->equalTo(150),
                        $this->equalTo(10)
                    );
                
        // Run the updateProduct method
        $this->controller->updateProduct();

        // Assert there are no errors
        $this->assertEmpty($this->controller->errors, "Expected no validation errors.");

        // Check that the redirect URL is set correctly (simulating a redirect to /products)
        $this->assertEquals('/products', $this->controller->redirectUrl, "Expected redirection to product listing page.");
    }

    // purchaseProduct
    public function testProcessPurchaseProductWithValidData()
    {
        // Arrange
        $this->sessionMock['user_id'] = 1;
        $this->controller = new ProductsController($this->productMock, $this->transactionMock, $this->sessionMock);

        $_POST['product_id'] = 1;
        $_POST['quantity'] = 2;

        // Mock product details
        $this->productMock->expects($this->once())
            ->method('getById')
            ->with($this->equalTo(1))
            ->willReturn(['id' => 1, 'name' => 'Test Product', 'price' => 100, 'quantity_available' => 5]);

        // Expect the quantity to be updated and a transaction to be created
        $this->productMock->expects($this->once())
            ->method('updateQuantity')
            ->with($this->equalTo(1), $this->equalTo(3)); // Reduces available quantity by 2

        $this->transactionMock->expects($this->once())
            ->method('logPurchase')
            ->with($this->equalTo(1), $this->equalTo(1), $this->equalTo(2), $this->equalTo(200));

        // Act
        $this->controller->processPurchase(1, 2);

        // Assert
        $this->assertEquals('/products/view?id=1', $this->controller->redirectUrl, "Expected redirection to /products after successful purchase.");
    }

    public function testPurchaseProductWithExcessiveQuantity()
    {
        // Arrange
        $this->sessionMock['user_id'] = 1;
        $this->controller = new ProductsController($this->productMock, $this->transactionMock, $this->sessionMock);

        $_POST['product_id'] = 1;
        $_POST['quantity'] = 10; // Requesting more than available stock

        // Mock product details
        $this->productMock->expects($this->once())
            ->method('getById')
            ->with($this->equalTo(1))
            ->willReturn(['id' => 1, 'name' => 'Test Product', 'price' => 100, 'quantity_available' => 5]);

        // Act
        $this->controller->processPurchase(1, 10);

        // Assert
        $this->assertContains("Insufficient stock for this product.", $this->controller->errors);
    }

    public function testPurchaseProductWithInvalidProductId()
    {
        // Arrange
        $this->sessionMock['user_id'] = 1;
        $this->controller = new ProductsController($this->productMock, $this->transactionMock, $this->sessionMock);

        $_POST['product_id'] = 999; // Non-existent product ID
        $_POST['quantity'] = 1;

        // Mock getById to return null, simulating a non-existent product
        $this->productMock->expects($this->once())
            ->method('getById')
            ->with($this->equalTo(999))
            ->willReturn(null);

        // Act
        $this->controller->processPurchase();

        // Assert
        $this->assertContains("Product not found.", $this->controller->errors);
        $this->assertEquals('/products/view?id=999', $this->controller->redirectUrl, "Expected redirection to /products after successful purchase.");
    }
}
?>
