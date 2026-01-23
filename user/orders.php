<?php
session_start();
include '../db.php';
include '../navbar.php';

/** * REMEDIATION: BROKEN ACCESS CONTROL (OWASP A01:2021)
 * Enforcing server-side session validation to ensure only authenticated 
 * users can access their order history[cite: 148].
 */
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];
?>

<h1>My Order History</h1>

<?php
try {
    /** * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
     * Replaced legacy mysqli_query with a PDO Prepared Statement.
     * Even though the source is a session variable, using placeholders (?) 
     * ensures the query logic is isolated from the data[cite: 1150].
     */
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE username = ? ORDER BY id DESC");
    $stmt->execute([$user]);

    // Check for records securely using PDO rowCount or fetching
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($orders)) {
        echo "<p>You have not placed any orders yet.</p>";
    } else {
        foreach ($orders as $o) {
            /** * REMEDIATION: CROSS-SITE SCRIPTING (XSS) PROTECTION
             * Applying htmlspecialchars() to all database outputs.
             * This prevents 'Stored XSS' where an attacker might have injected 
             * scripts into fields like the 'address'[cite: 148].
             */
            $safe_id = htmlspecialchars($o['id'], ENT_QUOTES, 'UTF-8');
            $safe_total = htmlspecialchars(number_format($o['total'], 2), ENT_QUOTES, 'UTF-8');
            $safe_address = htmlspecialchars($o['address'], ENT_QUOTES, 'UTF-8');

            echo "<div class='order-item' style='padding: 10px; border-bottom: 1px solid #ccc;'>";
            echo "<strong>Order #" . $safe_id . "</strong><br>";
            echo "Total Paid: RM " . $safe_total . "<br>";
            echo "Shipping Address: " . $safe_address . "<br>";
            echo "</div>";
        }
    }

} catch (PDOException $e) {
    /** * REMEDIATION: INFORMATION DISCLOSURE PREVENTION
     * Suppressing raw SQL error messages to prevent leaking database 
     * schemas to the end-user[cite: 1158].
     */
    error_log("Order Retrieval Error: " . $e->getMessage());
    echo "<p>Unable to retrieve orders at this time. Please try again later.</p>";
}
?>

<p><a href="../products.php">Return to Shop</a></p>
