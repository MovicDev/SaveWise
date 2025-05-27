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
$host = getenv('movicdev.render.com') ?: 'localhost';
$db = getenv('savewise_db') ?: 'savewise';
$user = getenv(name: 'savewise_user') ?: 'root';
$pass = getenv('savewise_pass') ?: '';
$port = getenv('5432') ?: '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db_status = "Connected to PostgreSQL!";
} catch (PDOException $e) {
    $db_status = "Connection failed: " . $e->getMessage();
}
