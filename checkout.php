<?php
session_start();
include 'db.php';

if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    echo "<a href='products.php'>Back to Products Catalog</a>";
    exit;
}

try {
    /** * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
     * Replaced vulnerable implode() and mysqli_query with a secure PDO IN().
     * This prevents attackers from manipulating session keys to execute SQL commands.
     */
    $cart_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($cart_ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($cart_ids);

    $total = 0;
    $products = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['quantity'] = $qty;
        $products[] = $row;
        $total += $row['price'] * $qty;
    }

    /* Process Order Placement */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {

        /** * REMEDIATION: SESSION INTEGRITY
         * Using the authenticated session username to prevent Guest spoofing 
         * or account impersonation. 
         */
        $user = $_SESSION['user'] ?? 'Guest_User';
        $address = trim($_POST['address']);

        /** * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
         * Using Prepared Statements for the INSERT operation.
         */
        $order_sql = "INSERT INTO orders (username, total, address) VALUES (?, ?, ?)";
        $stmtOrder = $pdo->prepare($order_sql);
        $stmtOrder->execute([$user, $total, $address]);

        $order_id = $pdo->lastInsertId();

        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmtItem = $pdo->prepare($item_sql);

        foreach ($products as $p) {
            $stmtItem->execute([$order_id, $p['id'], $p['quantity'], $p['price']]);
        }

        unset($_SESSION['cart']);

        /** * REMEDIATION: CROSS-SITE SCRIPTING (XSS) PROTECTION
         * Using htmlspecialchars() for all reflected output. 
         */
        echo "<h2>Order placed successfully</h2>";
        echo "<p>Total paid: RM " . htmlspecialchars(number_format($total, 2), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<a href='products.php'>Back to Products</a> | ";
        echo "<a href='user/orders.php'>View My Orders</a>";
        exit;
    }
} catch (PDOException $e) {
    /** * REMEDIATION: INFORMATION DISCLOSURE PREVENTION
     * Catching technical errors to prevent leaking database structure. 
     */
    error_log("Checkout Error: " . $e->getMessage());
    echo "<p>A system error occurred while processing your order.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TechNovation | Secure Checkout</title>
</head>
<body>

<h2>Secure Checkout</h2>

<p>Total amount due: <strong>RM <?php echo htmlspecialchars(number_format($total, 2), ENT_QUOTES, 'UTF-8'); ?></strong></p>

<form method="POST">
    <label for="address">Shipping Address:</label><br>
    <textarea name="address" id="address" rows="4" cols="50" required placeholder="Enter your full delivery address..."></textarea><br><br>
    <button name="checkout" type="submit" style="padding:10px 20px; background-color:#28a745; color:white; border:none; cursor:pointer;">Confirm & Place Order</button>
</form>

<br>
<a href="cart.php">← Return to Cart</a>

</body>
</html>
