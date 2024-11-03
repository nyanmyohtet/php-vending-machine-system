<?php

// Load database configuration
$dbConfig = require 'database.config.php';

// You can add other configurations here as needed
$config = [
    'site_title' => 'Vending Machine System',
];

// Merge database config into main config if needed
$config['database'] = $dbConfig;

return $config;
?>