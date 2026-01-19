<?php
include '../db.php';
include '../navbar.php';

$user = $_GET['user'];
?>

<h1>Orders for <?php echo $user; ?></h1>

<?php
$sql = "SELECT * FROM orders WHERE username='$user'";
$result = mysqli_query($conn, $sql);

while ($o = mysqli_fetch_assoc($result)) {
    echo "<p>";
    echo "Order ID: {$o['id']}<br>";
    echo "Total: RM {$o['total']}<br>";
    echo "Address: {$o['address']}<br>";
    echo "</p><hr>";
}
?>

<p><a href="users.php">← Back to Users</a></p>
