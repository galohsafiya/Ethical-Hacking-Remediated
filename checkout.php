<?php
session_start();
include 'db.php';

if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty</p>";
    echo "<a href='products.php'>Back to Products</a>";
    exit;
}

/* Calculate total */
$ids = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT * FROM products WHERE id IN ($ids)";
$result = mysqli_query($conn, $sql);

$total = 0;
$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $qty = $_SESSION['cart'][$row['id']];
    $row['quantity'] = $qty;
    $products[] = $row;
    $total += $row['price'] * $qty;
}

/* Place order */
if (isset($_POST['checkout'])) {

    $user = $_SESSION['user'] ?? 'guest';
    $address = $_POST['address'];

    // INTENTIONALLY VULNERABLE (SQLi)
    $sql = "INSERT INTO orders (username, total, address)
            VALUES ('$user', '$total', '$address')";
    mysqli_query($conn, $sql);

    $order_id = mysqli_insert_id($conn);

    foreach ($products as $p) {
        $pid = $p['id'];
        $qty = $p['quantity'];
        $price = $p['price'];

        mysqli_query(
            $conn,
            "INSERT INTO order_items (order_id, product_id, quantity, price)
             VALUES ($order_id, $pid, $qty, $price)"
        );
    }

    unset($_SESSION['cart']);

    echo "<h2>Order placed successfully</h2>";
    echo "<p>Total paid: RM $total</p>";
    echo "<a href='products.php'>Back to Products</a> | ";
    echo "<a href='user/orders.php'>View Orders</a>";
    exit;
}
?>

<h2>Checkout</h2>

<p>Total amount: <strong>RM <?php echo $total; ?></strong></p>

<form method="POST">
    Address:<br>
    <textarea name="address" required></textarea><br><br>
    <button name="checkout">Place Order</button>
</form>

<br>
<a href="cart.php">← Back to Cart</a>
