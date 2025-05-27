<!-- <?php
// session_start();
// $host = "localhost";
// $user = "root";
// $pass = "";
// $db   = "savewise";

// $conn = new mysqli($host, $user, $pass, $db);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
?> -->



<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();



$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$port = getenv('DB_PORT') ?: '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db_status = " Connected to PostgreSQL!";
} catch (PDOException $e) {
    $db_status = "Connection failed: " . $e->getMessage();
}

echo $db_status;
?>
