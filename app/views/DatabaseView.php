<?php

class DatabaseView
{
    public function render(string $message): void
    {
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
}
