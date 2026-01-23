<?php

include '../db.php';
include '../navbar.php';
?>

<h1>Product Management</h1>

<p>
    <a href="add_product.php">➕ Add New Product</a>
</p>

<hr>

<h2>Existing Products</h2>

<?php
try {
    /* * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
     * Replaced legacy mysqli_query with a PDO query.
     */
    $stmt = $pdo->query("SELECT * FROM products");

    while ($p = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        /* * REMEDIATION: CROSS-SITE SCRIPTING (XSS) PREVENTION (OWASP A03:2021)
         * We apply htmlspecialchars() to data retrieved from the database.
         */
        $safe_name = htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8');
        $safe_id = htmlspecialchars($p['id'], ENT_QUOTES, 'UTF-8');
        $safe_price = htmlspecialchars($p['price'], ENT_QUOTES, 'UTF-8');

        echo "<p>";
        echo "ID: " . $safe_id . " | ";
        echo "<strong>" . $safe_name . "</strong> | RM " . $safe_price . " ";
        echo "<a href='delete_product.php?id=" . $safe_id . "' onclick=\"return confirm('Are you sure?')\">[Delete]</a>";
        echo "</p>";
    }

} catch (PDOException $e) {
    /* * REMEDIATION: SECURITY MISCONFIGURATION / INFO DISCLOSURE
     * Instead of displaying raw SQL errors (which reveal table structures), 
     * we log the error internally and show a generic notification.
     */
    error_log("Database Error in products.php: " . $e->getMessage());
    echo "<p class='error'>Unable to load product catalog. Please contact the system administrator.</p>";
}
?>
