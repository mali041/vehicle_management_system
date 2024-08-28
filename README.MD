### 1. **`DatabaseController.php`**

```php
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
```

- **Purpose**: This class acts as a controller in the MVC (Model-View-Controller) pattern. It coordinates between the model and the view.
- **Constructor**: It accepts an instance of `DatabaseModel` and assigns it to the `$model` property.
- **`createDatabase()` Method**: Calls the `createDatabase()` method on the model. If an exception is thrown, it catches it and sets the `$message` property to the exception message.
- **`getMessage()` Method**: Returns the message stored in `$message`, which can be displayed to the user.

### 2. **`DatabaseModel.php`**

```php
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
```

- **Purpose**: This class handles database operations.
- **Constructor**: Takes a configuration array, sets the database name, and calls the `connect()` method to establish a database connection.
- **`connect()` Method**: Initializes a connection to the MySQL database using `mysqli`. Logs an error and throws an exception if the connection fails.
- **`createDatabase()` Method**: Executes a SQL query to create the database if it does not already exist. Returns a success or error message based on the outcome of the query.
- **`logError()` Method**: Writes error messages to a log file (`app.log`).
- **`__destruct()` Method**: Closes the database connection when the object is destroyed.

### 3. **`DatabaseView.php`**

```php
<?php

class DatabaseView
{
    public function render(string $message): void
    {
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
}
```

- **Purpose**: This class is responsible for rendering output to the user.
- **`render()` Method**: Outputs the message, ensuring it is safely encoded using `htmlspecialchars()` to prevent XSS attacks.

### 4. **`database.php` (Configuration File)**

```php
<?php

return [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'vehicle_management_system',
];
```

- **Purpose**: Provides the configuration settings for connecting to the database.
- **Returns an array** with database connection parameters like `host`, `username`, `password`, and `dbname`.

### 5. **`autoload.php`**

```php
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
```

- **Purpose**: Automatically loads class files when they are needed, without manually including them.
- **`spl_autoload_register`**: Registers an autoload function that searches for class files in the specified directories and includes the file if it exists.

### 6. **`index.php` (Entry Point)**

```php
<?php

require_once __DIR__ . '/../includes/autoload.php';

$config = require __DIR__ . '/../config/database.php';

$model = new DatabaseModel($config);
$controller = new DatabaseController($model);
$view = new DatabaseView();

$controller->createDatabase();

$view->render($controller->getMessage());
```

- **Purpose**: The entry point for the application.
- **Includes**: The autoloader to handle class loading.
- **Configuration**: Loads the database configuration.
- **Instantiation**: Creates instances of `DatabaseModel`, `DatabaseController`, and `DatabaseView`.
- **Action**: Calls `createDatabase()` on the controller, which in turn calls `createDatabase()` on the model. The result is rendered using the view.

### Summary

- **`DatabaseModel`** handles database operations.
- **`DatabaseController`** coordinates between the model and the view.
- **`DatabaseView`** displays messages to the user.
- **`autoload.php`** ensures classes are loaded automatically.
- **`index.php`** ties everything together by creating instances of the model, controller, and view, and performing the main action of creating the database and displaying the result.
