<?php

require_once __DIR__ . '/../includes/autoload.php';

$config = require __DIR__ . '/../config/database.php';

$model = new DatabaseModel($config);
$controller = new DatabaseController($model);
$view = new DatabaseView();

$controller->createDatabase();

$view->render($controller->getMessage());
