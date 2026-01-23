<?php

session_start();
include 'db.php';

/* REMEDIATION: BROKEN SESSION MANAGEMENT (OWASP A01:2021)
 * Standardizing cart initialization to ensure session data is handled 
 * as a structured array, preventing unauthorized manipulation.
 */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ADD item (Supports secure ID tracking) */
if (isset($_GET['id']) || isset($_GET['add'])) {
    $id = (int)($_GET['id'] ?? $_GET['add']);
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

/* DECREASE quantity */
if (isset($_GET['minus'])) {
    $id = (int)$_GET['minus'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }
}

/* REMOVE product entirely */
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
}

/* CLEAR entire cart */
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
}

/* UI Elements */
echo "<h2>Your Enterprise Cart</h2>";
echo "<a href='products.php'>← Back to Products Catalog</a><br><br>";

if (empty($_SESSION['cart'])) {
    echo "Your cart is currently empty.";
    exit;
}

try {
    /* * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
     * In Phase 2, dynamic IN() clauses were identified as high-risk entry points.
     * We replaced string with a placeholder-based approach. 
     * Since PDO does not natively bind arrays to IN(), we generate a string 
     * of '?' placeholders equal to the number of items in the cart.
     */
    $cart_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($cart_ids) - 1) . '?';
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($cart_ids);

    $total = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $qty = $_SESSION['cart'][$id];
        $subtotal = $row['price'] * $qty;

        /* * REMEDIATION: CROSS-SITE SCRIPTING (XSS) PROTECTION
         * Applying htmlspecialchars() to ensure product names cannot 
         * execute malicious scripts in the user's browser.
         */
        $safe_name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $safe_price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');

        echo "<div class='cart-item'>";
        echo "<p><strong>" . $safe_name . "</strong><br>";
        echo "Price: RM " . $safe_price . "<br>";
        echo "Quantity: " . (int)$qty . "<br>";
        echo "Subtotal: RM " . number_format($subtotal, 2) . "<br>";

        echo "<a href='cart.php?add=$id'>[ + ]</a> ";
        echo "<a href='cart.php?minus=$id'>[ − ]</a> ";
        echo "<a href='cart.php?remove=$id'>[ Remove ]</a>";
        echo "</p><hr></div>";

        $total += $subtotal;
    }

    echo "<h3>Grand Total: RM " . number_format($total, 2) . "</h3>";
    echo "<a href='cart.php?clear=1' onclick=\"return confirm('Clear all items?')\">Clear Cart</a>";
    echo "<br><br><a href='checkout.php' style='padding:10px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Proceed to Secure Checkout</a>";

} catch (PDOException $e) {
    /* * REMEDIATION: INFORMATION DISCLOSURE PREVENTION
     * Catching technical database errors to hide system paths and 
     * table structures from potential attackers.
     */
    error_log("Cart Error: " . $e->getMessage());
    echo "<p>A system error occurred. Your cart data is safe, but we cannot display it right now.</p>";
}
