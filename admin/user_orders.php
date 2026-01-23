<?php

include '../db.php';
include '../navbar.php';

/* * REMEDIATION: CROSS-SITE SCRIPTING (XSS) PREVENTION (OWASP A03:2021)
 * We sanitize the user parameter from the URL before displaying it. 
 * This prevents attackers from injecting malicious scripts into the page header.
 */
$user = isset($_GET['user']) ? htmlspecialchars($_GET['user'], ENT_QUOTES, 'UTF-8') : 'Unknown User';
?>

<h1>Orders for <?php echo $user; ?></h1>

<?php
try {
    /* * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
     * Replaced the vulnerable mysqli_query concatenation with a PDO Prepared Statement.
     * By using placeholders (?), we ensure that the username is treated as a 
     * literal string, neutralizing bypass attempts like ' OR '1'='1.
     */
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE username = ?");
    $stmt->execute([$_GET['user']]);

    $ordersFound = false;

    while ($o = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ordersFound = true;
        
        /* * REMEDIATION: OUTPUT ENCODING (XSS DEFENSE)
         * Data from the database is encoded before rendering to ensure 
         * stored payloads cannot execute in the admin's browser. 
         */
        $safe_id = htmlspecialchars($o['id'], ENT_QUOTES, 'UTF-8');
        $safe_total = htmlspecialchars($o['total'], ENT_QUOTES, 'UTF-8');
        $safe_address = htmlspecialchars($o['address'], ENT_QUOTES, 'UTF-8');

        echo "<div class='order-record'>";
        echo "<p><strong>Order ID:</strong> " . $safe_id . "<br>";
        echo "<strong>Total:</strong> RM " . $safe_total . "<br>";
        echo "<strong>Address:</strong> " . $safe_address . "<br>";
        echo "</p><hr></div>";
    }

    if (!$ordersFound) {
        echo "<p>No orders found for this account.</p>";
    }

} catch (PDOException $e) {
    /* * REMEDIATION: INFORMATION DISCLOSURE PREVENTION
     * Catching system errors prevents the leaking of table structures or 
     * database details to the end-user.
     */
    error_log("Database Error in user_orders.php: " . $e->getMessage());
    echo "<p>A system error occurred while retrieving order data.</p>";
}
?>

<p><a href="users.php">← Back to Users</a></p>
