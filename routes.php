<?php
$routes = [
    'GET /auth/register' => ['AuthController', 'registerPage'],
    'POST /auth/register' => ['AuthController', 'register'],
    'GET /auth/login' => ['AuthController', 'loginPage'],
    'POST /auth/login' => ['AuthController', 'login'],
    'POST /auth/logout' => ['AuthController', 'logout'],
    'GET /products' => ['ProductsController', 'index'],
    'GET /products/add' => ['ProductsController', 'addProduct'],
    'POST /products/create' => ['ProductsController', 'createProduct'],
    'GET /products/view' => ['ProductsController', 'viewProduct'],
    'GET /products/edit' => ['ProductsController', 'editProduct'],
    'POST /products/update' => ['ProductsController', 'updateProduct'],
    'GET /products/delete' => ['ProductsController', 'deleteProduct'],
    'GET /products/purchase/([0-9]+)/([a-zA-Z0-9-]+)' => ['ProductsController', 'purchaseProduct'],
    'POST /products/processPurchase' => ['ProductsController', 'processPurchase'],

    // API routes
    'POST /api/v1/auth/login' => ['AuthApiController', 'login'],
    
    'GET /api/v1/products' => ['ProductsApiController', 'getProducts'],
    // /api/v1/products/{id}
    'GET /api/v1/products/([0-9]+)' => ['ProductsApiController', 'getProductDetails'],
    'POST /api/v1/products' => ['ProductsApiController', 'addProduct'],
    'PUT /api/v1/products/([0-9]+)' => ['ProductsApiController', 'updateProduct'],
    'DELETE /api/v1/products/([0-9]+)' => ['ProductsApiController', 'deleteProduct'],
];
?>