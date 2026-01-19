<?php
include 'navbar.php';
include 'db.php'; // Ensure db.php now defines a PDO connection as $pdo

// Get search query and set to empty string if not provided
$q = isset($_GET['q']) ? $_GET['q'] : '';

// 1. FIX XSS: Use htmlspecialchars() to encode user input before display
if (!empty($q)) {
    $safe_q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
    echo "<p>Search results for: <strong>$safe_q</strong></p>";
}

// 2. FIX SQL INJECTION: Use a PDO Prepared Statement
// Use placeholders (?) instead of variables directly in the query string
$sql = "SELECT * FROM products WHERE name LIKE ?";
$stmt = $pdo->prepare($sql);

// Add wildcards to the search term
$searchTerm = "%$q%";
$stmt->execute([$searchTerm]);

// Fetch all results
$products = $stmt->fetchAll();
?>

<h2>Product Catalog</h2>
<a href="cart.php">View Cart</a>
<hr>

<form method="GET">
    <input type="text" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search products">
    <button type="submit">Search</button>
</form>

<hr>

<?php
// Loop through the secured results
if (count($products) > 0) {
    foreach ($products as $row) {
        // Encode database outputs as a best practice for "Defense in Depth"
        echo "<h3>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</h3>";
        echo "<p>Price: RM " . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<a href='cart.php?id=" . (int)$row['id'] . "'>Add to Cart</a>";
        echo "<hr>";
    }
} else {
    echo "<p>No products found matching your search.</p>";
}
?>
