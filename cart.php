<?php
session_start();
include 'db.php';

/* Always ensure cart exists */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ADD item (supports ?id= AND ?add=) */
if (isset($_GET['id']) || isset($_GET['add'])) {
    $id = $_GET['id'] ?? $_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

/* DECREASE quantity */
if (isset($_GET['minus'])) {
    $id = $_GET['minus'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }
}

/* REMOVE product entirely */
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
}

/* CLEAR entire cart */
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
}

/* UI */
echo "<h2>Your Cart</h2>";
echo "<a href='products.php'>← Back to Products</a><br><br>";

if (empty($_SESSION['cart'])) {
    echo "Cart is empty";
    exit;
}

/* Fetch products */
$ids = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT * FROM products WHERE id IN ($ids)";
$result = mysqli_query($conn, $sql);

$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $qty = $_SESSION['cart'][$id];
    $subtotal = $row['price'] * $qty;

    echo "<p>";
    echo "<strong>{$row['name']}</strong><br>";
    echo "Price: RM {$row['price']}<br>";
    echo "Quantity: $qty<br>";
    echo "Subtotal: RM $subtotal<br>";

    echo "<a href='cart.php?add=$id'>[ + ]</a> ";
    echo "<a href='cart.php?minus=$id'>[ − ]</a> ";
    echo "<a href='cart.php?remove=$id'>[ Remove ]</a>";

    echo "</p><hr>";

    $total += $subtotal;
}

echo "<h3>Total: RM $total</h3>";
echo "<a href='cart.php?clear=1'>Clear Cart</a>";
echo "<br><br><a href='checkout.php'>Proceed to Checkout</a>";
