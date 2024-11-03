<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-start mb-4">Product Details</h1>

    <?php include_once __DIR__ . '/../common/error.php'; ?>

    <?php if (!isset($errors) || count($errors) === 0): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
            <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <p class="card-text"><strong>Quantity Available:</strong> <?php echo htmlspecialchars($product['quantity_available']); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <a href="/products" class="btn btn-secondary">Back to List</a>
</div>
<?php include_once __DIR__ . '/../common/footer.php'; ?>
