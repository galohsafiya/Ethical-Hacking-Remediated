<?php
include 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$u' AND password='$p'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) > 0) {
        $_SESSION['user'] = $u;
        $message = "Login successful. Welcome $u";
    } else {
        $message = "Login failed";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<?php if ($message): ?>
<p><strong><?php echo $message; ?></strong></p>
<?php endif; ?>

<h2>Login</h2>

<form method="POST">
    Username: <input name="username"><br><br>
    Password: <input name="password"><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
