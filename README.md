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

**Creating the Tables**
   ```sql
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        quantity_available INT NOT NULL
    );

    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('Admin', 'User') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        user_id INT,
        quantity INT NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    );
   ```

After running this scripts, the database will have the `products`, `users`, and `transactions` tables set up with the required fields and relationships.

### 4. Start the Development Server

Run the application using PHP's built-in server. In the terminal, execute:

```bash
php -S localhost:8000
```

This command will start the application at `http://localhost:8000/products`.

### 5. Access the Application

Once the server is running, open the web browser and go to:

`http://localhost:8000/products`

This will display the product listing page.

### 6. Access the Deployed Application

Access the Deployed Application at: 

`http://165.22.96.65/products`

Login Information

To access all features, use the following login credentials:

Username: admin

Password: admin123

>Note: Update these credentials as needed in the database for security.

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
## How set up the database for this PHP application

To set up the database for this PHP application, used PHP's PDO (PHP Data Objects) and MySQL, including three main tables: `products`, `users`, and `transactions`.

### Database Overview

The database consists of three tables:
1. **Products**: Stores details of each product available in the vending machine.
2. **Users**: Stores information about users who interact with the vending machine system, including their roles.
3. **Transactions**: Logs each purchase transaction, connecting users to the products they buy.

### Table Definitions

1. **Products Table**
    - **Purpose**: Holds all available products with their names, prices, and quantities.
    - **Structure**:
      ```sql
      CREATE TABLE products (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(50) NOT NULL,
          price DECIMAL(10, 2) NOT NULL,
          quantity_available INT NOT NULL
      );
      ```
    - **Fields**:
      - `id`: Primary key, auto-incrementing integer.
      - `name`: Name of the product, up to 50 characters.
      - `price`: Price per unit of the product, with two decimal places for precision.
      - `quantity_available`: The current stock quantity for each product.

2. **Users Table**
    - **Purpose**: Stores user accounts, including both admin and regular users.
    - **Structure**:
      ```sql
      CREATE TABLE users (
          id INT AUTO_INCREMENT PRIMARY KEY,
          username VARCHAR(50) UNIQUE NOT NULL,
          password VARCHAR(255) NOT NULL,
          role ENUM('Admin', 'User') NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
      ```
    - **Fields**:
      - `id`: Primary key, auto-incrementing integer.
      - `username`: Unique username for each user.
      - `password`: Hashed password for security.
      - `role`: User role, restricted to either `Admin` or `User`.
      - `created_at`: Timestamp indicating when the user account was created, with a default value of the current timestamp.

3. **Transactions Table**
    - **Purpose**: Logs each purchase, associating users with the products they bought and recording the details.
    - **Structure**:
      ```sql
      CREATE TABLE transactions (
          id INT AUTO_INCREMENT PRIMARY KEY,
          product_id INT,
          user_id INT,
          quantity INT NOT NULL,
          total_price DECIMAL(10, 2) NOT NULL,
          purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (product_id) REFERENCES products(id),
          FOREIGN KEY (user_id) REFERENCES users(id)
      );
      ```
    - **Fields**:
      - `id`: Primary key, auto-incrementing integer.
      - `product_id`: Foreign key linking to the `products` table.
      - `user_id`: Foreign key linking to the `users` table.
      - `quantity`: Number of units of the product purchased in the transaction.
      - `total_price`: Total cost of the transaction, calculated as `price * quantity`.
      - `purchase_date`: Timestamp of the transaction, defaulted to the current timestamp.
    - **Relationships**:
      - `product_id` is a foreign key referencing the `id` in the `products` table.
      - `user_id` is a foreign key referencing the `id` in the `users` table.

### Setting Up the Database with PDO

To connect this database in PHP, created a `Database.php` script that connects to MySQL using PDO.

1. **Database Connection Using PDO**
   ```php
    class Database {
        private string $host;
        private string $port;
        private string $dbName;
        private string $username;
        private string $password;
        public ?PDO $conn = null;

        public function __construct() {
            $config = require 'config.php';
            
            $this->host = $config['database']['host'];
            $this->port = $config['database']['port'];
            $this->dbName = $config['database']['db_name'];
            $this->username = $config['database']['username'];
            $this->password = $config['database']['password'];
        }

        public function getConnection(): ?PDO {
            if ($this->conn === null) {
                try {
                    $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName}";
                    $this->conn = new PDO($dsn, $this->username, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $exception) {
                    error_log("Connection error: " . $exception->getMessage());
                    throw new RuntimeException("Database connection failed.");
                }
            }
            return $this->conn;
        }
    }
   ```