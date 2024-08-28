<?php

class DatabaseController
{
    private $model;
    private $message = '';

    public function __construct(DatabaseModel $model)
    {
        $this->model = $model;
    }

    public function createDatabase(): void
    {
        try {
            $this->model->createDatabase();
            $this->message = "Database created successfully.";
        } catch (Exception $e) {
            $this->message = "Error creating database: " . $e->getMessage();
        }
    }

    public function executeSqlFile(string $filePath): void
    {
        try {
            $result = $this->model->executeSqlFile($filePath);
            $this->message = $result;
        } catch (Exception $e) {
            $this->message = "Error executing SQL file: " . $e->getMessage();
        }
    }
    public function getMessage(): string
    {
        return $this->message;
    }
}
