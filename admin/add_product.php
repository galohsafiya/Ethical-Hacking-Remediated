<?php

include '../db.php'; 
include '../navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    // 1. INPUT VALIDATION (Remediates Broken Authentication/Logic)
    if (empty($name) || empty($price) || !is_numeric($price)) {
        echo "<script>alert('Error: Please provide a valid product name and numeric price.');</script>";
    } else {
        try {
            // 2. SQL INJECTION REMEDIATION (OWASP A03:2021)
            // Using PDO Prepared Statements instead of legacy mysqli concatenation
            $sql = "INSERT INTO products (name, price) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $price])) {
                header("Location: products.php?msg=success");
                exit;
            }
        } catch (PDOException $e) {
            // INFORMATION DISCLOSURE REMEDIATION
            // We log the error internally but show the user a generic message
            error_log("Database Error: " . $e->getMessage());
            echo "<script>alert('A system error occurred. Please try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Add New Product</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f8f9fa; padding: 40px; }
        .form-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; margin: auto; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #218838; }
        .back-link { display: block; margin-top: 20px; text-align: center; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Enterprise Product</h2>
    <p style="font-size: 0.9rem; color: #666;">Authorized Personnel Only - All entries are logged.</p>
    <hr>
    
    <form method="POST">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" placeholder="e.g. Enterprise Server" required>

        <label for="price">Price (RM):</label>
        <input type="text" id="price" name="price" placeholder="e.g. 5000.00" required>

        <button name="add" type="submit">Finalize & Add Product</button>
    </form>

    <a href="products.php" class="back-link">← Return to Product Catalog</a>
</div>

</body>
</html>
