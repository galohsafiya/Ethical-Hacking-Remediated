<?php
include '../db.php';
include '../navbar.php';
?>

<h1>Product Management</h1>

<p>
    <a href="add_product.php">➕ Add New Product</a> |
</p>

<hr>

<h2>Existing Products</h2>

<?php
$result = mysqli_query($conn, "SELECT * FROM products");

while ($p = mysqli_fetch_assoc($result)) {
    echo "<p>";
    echo "ID: {$p['id']} | ";
    echo "{$p['name']} | RM {$p['price']} ";
    echo "<a href='delete_product.php?id={$p['id']}'>[Delete]</a>";
    echo "</p>";
}
?>
