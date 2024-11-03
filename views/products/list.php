<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-start mb-4">Product List</h1>

    <!-- <div class="mb-3">
        <strong>Sort by:</strong>
    </div> -->

    <div class="d-flex justify-content-end mb-3">
        <!-- Sort By Section -->
        <div class="dropdown me-2">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Select Sorting Option
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item" href="?sort=name&order=asc">Name Ascending</a></li>
                <li><a class="dropdown-item" href="?sort=name&order=desc">Name Descending</a></li>
                <li><a class="dropdown-item" href="?sort=price&order=asc">Price Ascending</a></li>
                <li><a class="dropdown-item" href="?sort=price&order=desc">Price Descending</a></li>
                <li><a class="dropdown-item" href="?sort=quantity_available&order=asc">Quantity Ascending</a></li>
                <li><a class="dropdown-item" href="?sort=quantity_available&order=desc">Quantity Descending</a></li>
            </ul>
        </div>
        
        <!-- Add New Product Button -->
        <?php if (isset($this->session['user_role']) && $this->session['user_role'] === 'Admin'): ?>
        <a href="/products/add" class="btn btn-primary">Add Product</a>
        <?php endif; ?>
    </div>

    <table class="table table-striped table-hover table-bordered caption-top">  
        <caption>List of products</caption>
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Name</th>
                <th class="text-center">Price</th>
                <th class="text-center">Quantity Available</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td class="text-end"><?php echo htmlspecialchars($product['id']); ?>.</td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td class="text-end"><?php echo htmlspecialchars(number_format($product['price'], 2)); ?>$</td>
                <td class="text-end"><?php echo htmlspecialchars($product['quantity_available']); ?></td>
                <td>
                    <a href="/products/view?id=<?php echo $product['id']; ?>">View</a>
                    <a href="/products/edit?id=<?php echo $product['id']; ?>">Edit</a>
                    <a href="/products/purchase/<?php echo $product['id']; ?>/<?php echo urlencode(strtolower(str_replace(' ', '-', $product['name']))); ?>">Purchase</a>
                    <a href="/products/delete?id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="/products?page=<?php echo $page - 1; ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <li class="page-item active"><span class="page-link"><?php echo $i; ?></span></li>
                <?php else: ?>
                    <li class="page-item"><a class="page-link" href="/products?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="/products?page=<?php echo $page + 1; ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include_once __DIR__ . '/../common/footer.php'; ?>
