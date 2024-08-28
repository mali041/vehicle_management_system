<?php

require_once __DIR__ . '/../includes/autoload.php';

$config = require __DIR__ . '/../config/database.php';

try {
    $model = new DatabaseModel($config);
    $controller = new DatabaseController($model);
    $view = new DatabaseView();

    $controller->createDatabase();
    $view->render($controller->getMessage());

    $sqlFilePath = __DIR__ . '/../database.sql';
    $controller->executeSqlFile($sqlFilePath);
    $view->render($controller->getMessage());
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
