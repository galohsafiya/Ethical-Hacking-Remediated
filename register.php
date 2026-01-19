<?php
include 'db.php';

if (isset($_POST['register'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // INTENTIONALLY VULNERABLE
    $sql = "INSERT INTO users (username, password, role)
            VALUES ('$u', '$p', 'user')";
    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<form method="POST">
    Username: <input name="username"><br><br>
    Password: <input type="password" name="password"><br><br>
    <button name="register">Register</button>
</form>

<p><a href="index.php">Back to Login</a></p>

</body>
</html>
