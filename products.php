<?php
include 'navbar.php';
include 'db.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';
//XSS
if (!empty($q)) {
    echo "<p>Search results for: $q</p>";
}

$sql = "SELECT * FROM products WHERE name LIKE '%$q%'";
$result = mysqli_query($conn, $sql);
?>

<h2>Product Catalog</h2>
<a href="cart.php">View Cart</a>
<hr>

<form method="GET">
    <input type="text" name="q" placeholder="Search products">
    <button type="submit">Search</button>
</form>

<hr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
    echo "<h3>" . $row['name'] . "</h3>";
    echo "<p>Price: RM " . $row['price'] . "</p>";
    echo "<a href='cart.php?id=" . $row['id'] . "'>Add to Cart</a>";
    echo "<hr>";
}
?>
