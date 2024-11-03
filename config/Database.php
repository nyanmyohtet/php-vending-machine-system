<?php

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
