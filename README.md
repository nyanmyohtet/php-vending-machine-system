# Vending Machine System

A simple PHP-based Vending Machine System that runs on `PHP 8.3` and uses `MySQL 8` as its database. The app allows users to browse and manage products, and it serves as an example of how to structure a basic PHP project with a database connection.

## Prerequisites

Before running this application, make sure the following installed:

- **PHP 8.3** (https://www.php.net/downloads.php)
- **MySQL 8** (https://dev.mysql.com/downloads/mysql/)
- **Composer 2** (https://getcomposer.org/download/)

## Setup Instructions

Follow these steps to set up and run the application:

### 1. Clone the Repository

```bash
git clone <repository_url>
cd <repository_folder>
```

### 2. Install Dependencies

This project uses Composer for dependency management. Run the following command to install all required packages:

```bash
composer install --no-scripts
```

### 3. Configure the Database

Create a MySQL database and user for the application. Then, edit the `database.config.php` file with the appropriate connection details:

```php
// database.config.php
<?php
return [
    'host' => 'localhost',
    'port' => '3306',
    'db_name' => 'vending_machine_system',
    'username' => 'db_username',
    'password' => 'db_password',
];
```

### 4. Start the Development Server

Run the application using PHP's built-in server. In the terminal, execute:

```bash
php -S localhost:8000
```

This command will start the application at `http://localhost:8000`.

### 5. Access the Application

Once the server is running, open the web browser and go to:

```
http://localhost:8000/products
```

This will display the product listing page.

## Usage

Once the app is running, you can view, add, and manage products by interacting with the interface at `localhost:8000/products`.

## Troubleshooting

- **Database Connection Errors**: Ensure your MySQL server is running and `database.config.php` has the correct credentials.
- **Port Conflicts**: If `localhost:8000` is already in use, specify a different port in the `php -S` command, like `php -S localhost:8080`.

## Running Unit Tests

```sh
# check phpunit version
./vendor/bin/phpunit --version

# run all tests in the directory
./vendor/bin/phpunit tests/
```
