<?php
header("Content-Type: application/json");

include '../db.php';

/*
 OPTIONAL search parameter:
 /api/products.php?q=laptop
*/
$q = $_GET['q'] ?? '';

if ($q) {
    // ❗ INTENTIONALLY VULNERABLE (SQL Injection)
    $sql = "SELECT * FROM products WHERE name LIKE '%$q%'";
} else {
    $sql = "SELECT * FROM products";
}

$result = mysqli_query($conn, $sql);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($products),
    "products" => $products
]);
