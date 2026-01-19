<?php
include '../db.php';
include '../navbar.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // INTENTIONALLY VULNERABLE (SQLi + no validation)
    $sql = "INSERT INTO products (name, price)
            VALUES ('$name', '$price')";
    mysqli_query($conn, $sql);

    header("Location: products.php");
    exit;
}
?>

<h1>Add New Product</h1>

<form method="POST">
    Product Name:<br>
    <input name="name"><br><br>

    Price (RM):<br>
    <input name="price"><br><br>

    <button name="add">Add Product</button>
</form>

<p><a href="products.php">← Back to Products</a></p>
