<?php
// db.php
$host = 'localhost';
$db   = 'technovation';
$user = 'techuser';  // Change 'root' to 'techuser'
$pass = 'techpass';  // Change to 'techpass'
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // In Phase 3, we hide the raw error from the user
     die("A system error occurred. Please try again later."); 
}
?>
