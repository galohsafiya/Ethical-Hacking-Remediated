<?php
session_start();

$conn = mysqli_connect("localhost", "techuser", "techpass", "technovation");

if (!$conn) {
die("Database connection failed: " . mysqli_connect_error());
}
?>
