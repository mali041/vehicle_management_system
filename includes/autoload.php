<?php

spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/views/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
