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
            $this->message = $this->model->createDatabase();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
