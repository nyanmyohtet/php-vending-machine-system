<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-start mb-4">Add New Product</h2>
    <form action="/products/create" method="post">

        <?php include_once __DIR__ . '/../common/error.php'; ?>

        <div class="mb-3">
            <label for="productName" class="form-label">Name:</label>
            <input type="text" id="productName" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="productPrice" class="form-label">Price:</label>
            <input type="number" id="productPrice" min="0.01" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="productQuantity" class="form-label">Quantity Available:</label>
            <input type="number" id="productQuantity" min="1" name="quantity" class="form-control" required>
        </div>
        
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Add Product</button>
        </div>
    </form>
</div>
<?php include_once __DIR__ . '/../common/footer.php'; ?>