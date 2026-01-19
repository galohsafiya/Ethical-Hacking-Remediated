<?php
include '../db.php';

$user = $_GET['user'];

/* Delete user's orders */
mysqli_query($conn, "DELETE FROM orders WHERE username='$user'");

/* Delete user */
mysqli_query($conn, "DELETE FROM users WHERE username='$user'");

header("Location: users.php");
exit;
