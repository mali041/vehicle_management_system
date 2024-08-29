<?php

class DatabaseModel
{
    private $dbConnection;
    private $dbname;

    public function __construct(DatabaseConnection $dbConnection, array $config)
    {
        $this->dbConnection = $dbConnection;
        $this->dbname = $config['dbname'] ?? '';

        if (empty($this->dbname)) {
            throw new Exception("Database name is not provided in the configuration.");
        }

        $this->createDatabase(); // Ensure the database exists
    }

    public function createDatabase(): void
    {
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbConnection->getConnection()->real_escape_string($this->dbname);
        $result = $this->dbConnection->getConnection()->query($sql);

        if ($result === TRUE) {
            $this->dbConnection->selectDatabase($this->dbname);
        } else {
            throw new Exception("Error creating database: " . $this->dbConnection->getConnection()->error);
        }
    }

    public function executeSqlFile(string $filePath): string
    {
        try {
            $sql = file_get_contents($filePath);
            if ($sql === false) {
                throw new Exception("Error reading SQL file: " . $filePath);
            }

            $this->dbConnection->selectDatabase($this->dbname);
            $queries = $this->splitSqlFile($sql);
            $messages = [];

            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) {
                    continue;
                }

                try {
                    if (stripos($query, 'CREATE TABLE') === 0 || stripos($query, 'INSERT INTO') === 0 || stripos($query, 'ALTER TABLE') === 0) {
                        $tableName = $this->getTableNameFromQuery($query);
                        if ($tableName) {
                            if ($this->tableExists($tableName)) {
                                $messages[] = "Table '$tableName' already exists.";
                            } else {
                                if ($this->dbConnection->getConnection()->query($query) === TRUE) {
                                    $messages[] = "Table '$tableName' created successfully.";
                                } else {
                                    throw new Exception("Error executing query: " . $this->dbConnection->getConnection()->error);
                                }
                            }
                        } else {
                            throw new Exception("Could not determine table name from query.");
                        }
                    }
                } catch (Exception $e) {
                    $this->logError($e->getMessage() . "\nSQL: " . $query);
                    $messages[] = $e->getMessage();
                }
            }

            return implode(PHP_EOL, $messages);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function splitSqlFile(string $sql): array
    {
        return preg_split('/;\s*/', $sql, -1, PREG_SPLIT_NO_EMPTY);
    }

    private function getTableNameFromQuery(string $query): string
    {
        if (preg_match('/CREATE TABLE(?: IF NOT EXISTS)?\s+?(\w+)?\s*\(/i', $query, $matches)) {
            return $matches[1];
        }
        return '';
    }

    private function tableExists(string $tableName): bool
    {
        $result = $this->dbConnection->getConnection()->query("SHOW TABLES LIKE '" . $this->dbConnection->getConnection()->real_escape_string($tableName) . "'");
        return $result && $result->num_rows > 0;
    }

    private function logError(string $message): void
    {
        file_put_contents(__DIR__ . '/../logs/app.log', date('[Y-m-d H:i:s]') . " " . $message . PHP_EOL, FILE_APPEND);
    }
}
