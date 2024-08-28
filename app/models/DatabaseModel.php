<?php

class DatabaseModel
{
    private $conn;
    private $dbname;

    public function __construct(array $config)
    {
        $this->dbname = $config['dbname'];
        $this->connect($config);
    }

    private function connect(array $config): void
    {
        $this->conn = new mysqli($config['host'], $config['username'], $config['password']);

        if ($this->conn->connect_error) {
            $this->logError("Connection failed: " . $this->conn->connect_error);
            throw new Exception("Database connection failed.");
        }
    }

    public function createDatabase(): string
    {
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->conn->real_escape_string($this->dbname);
        if ($this->conn->query($sql) === TRUE) {
            return "Database created successfully.";
        } else {
            $this->logError("Error creating database: " . $this->conn->error);
            return "Error creating database.";
        }
    }

    private function logError(string $message): void
    {
        file_put_contents(__DIR__ . '/../logs/app.log', date('[Y-m-d H:i:s]') . " " . $message . PHP_EOL, FILE_APPEND);
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}
