<?php
// Load .env variables manually to test raw connection
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die(".env file not found at $envFile");
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = explode('=', $line, 2);
    $env[trim($name)] = trim($value);
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? 3306;
$db   = $env['DB_DATABASE'] ?? 'forge';
$user = $env['DB_USERNAME'] ?? 'forge';
$pass = $env['DB_PASSWORD'] ?? '';

echo "<h1>Database Connection Test</h1>";
echo "<p>Read config from .env:</p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Port: $port</li>";
echo "<li>User: $user</li>";
echo "<li>Database: $db</li>";
echo "</ul>";

echo "<h2>Attempting Connection...</h2>";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h3 style='color: green'>✅ Connection Successful!</h3>";
    echo "<p>The database is accessible with these credentials.</p>";
} catch (PDOException $e) {
    echo "<h3 style='color: red'>❌ Connection Failed</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    
    // Suggest fixes
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<hr><p><strong>Suggestion:</strong> 'Connection refused' usually means the MySQL service is not running or you are using the wrong Host.</p>";
        echo "<ul>";
        echo "<li>If Host is 127.0.0.1, try changing .env DB_HOST to <strong>localhost</strong>.</li>";
        echo "<li>If Host is localhost, try changing .env DB_HOST to <strong>127.0.0.1</strong>.</li>";
        echo "<li>Check if MySQL service is running on the server.</li>";
        echo "</ul>";
    }
}
