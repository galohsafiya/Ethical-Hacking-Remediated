<?php
include '../db.php';
include '../navbar.php';
?>

<h1>Users</h1>

<?php
$result = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin'");

while ($u = mysqli_fetch_assoc($result)) {
    echo "<p>";
    echo "ID: {$u['id']} | ";
    echo "Username: {$u['username']} | ";
    echo "Role: {$u['role']} ";
    echo "<a href='user_orders.php?user={$u['username']}'>[View Orders]</a> ";
    echo "<a href='delete_user.php?user={$u['username']}'>[Delete User]</a>";
    echo "</p>";
}
?>
