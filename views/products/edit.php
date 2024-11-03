<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-start mb-4">Edit Product</h1>
    <form action="/products/update?id=<?php echo $product['id']; ?>" method="post">

        <?php include_once __DIR__ . '/../common/error.php'; ?>

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input type="number" min="0.01" step="0.01" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity Available:</label>
            <input type="number" min="1" name="quantity" id="quantity" class="form-control" value="<?php echo htmlspecialchars($product['quantity_available']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
<?php include_once __DIR__ . '/../common/footer.php'; ?>