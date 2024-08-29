<?php

class DatabaseConnection
{
    private $conn;

    public function __construct(array $config)
    {
        $this->connect($config);
    }

    private function connect(array $config): void
    {
        $this->conn = new mysqli($config['host'], $config['username'], $config['password']);

        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function selectDatabase(string $dbname): void
    {
        $this->conn->select_db($dbname);
    }

    public function getConnection(): mysqli
    {
        return $this->conn;
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
