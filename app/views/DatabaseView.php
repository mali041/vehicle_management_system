<?php

class DatabaseView
{
    public function render(string $message): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
}
