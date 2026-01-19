<?php
$host = 'localhost';
$db   = 'technovation';
$user = 'techuser'; // Restored from your old db.php
$pass = 'techpass'; // Restored from your old db.php
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
     // During Phase 3 testing, uncomment the line below to see the error
     // echo "Connection failed: " . $e->getMessage(); 
     die("A database error occurred."); 
}
?>
