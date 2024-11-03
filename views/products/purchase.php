<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-start mb-4">Purchase <?= $product['name']; ?></h2>
    <p><strong>Price per unit:</strong> $<?= number_format($product['price'], 2); ?></p>
    <p><strong>Quantity Available:</strong> <?= htmlspecialchars($product['quantity_available']); ?></p>

    <form action="/products/processPurchase" method="post" class="mt-4">
        <?php include_once __DIR__ . '/../common/error.php'; ?>

        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']); ?>">

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity to Purchase:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" max="<?= htmlspecialchars($product['quantity_available']); ?>" required>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="/products" class="btn btn-secondary">Back to Product List</a>
            <button type="submit" class="btn btn-primary">Confirm Purchase</button>
        </div>
    </form>

<?php include_once __DIR__ . '/../common/footer.php'; ?>
