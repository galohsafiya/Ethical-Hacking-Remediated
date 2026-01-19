<?php
session_start();
include '../db.php';
include '../navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];
?>

<h1>My Orders</h1>

<?php
$result = mysqli_query(
    $conn,
    "SELECT * FROM orders WHERE username='$user' ORDER BY id DESC"
);

if (mysqli_num_rows($result) == 0) {
    echo "<p>You have not placed any orders yet.</p>";
}

while ($o = mysqli_fetch_assoc($result)) {
    echo "<p>";
    echo "<strong>Order #{$o['id']}</strong><br>";
    echo "Total: RM {$o['total']}<br>";
    echo "Address: {$o['address']}<br>";
    echo "</p><hr>";
}
?>
