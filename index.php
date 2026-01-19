<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // INTENTIONALLY VULNERABLE (SQL Injection)
    $sql = "SELECT * FROM users WHERE username='$u' AND password='$p'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 1) {
        $user = mysqli_fetch_assoc($res);

        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/admin.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TechNovation Solutions | Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .login-box {
            width: 350px;
            margin: 120px auto;
            padding: 25px;
            background: white;
            border: 1px solid #ccc;
            text-align: center;
        }

        h1 {
            margin-bottom: 5px;
        }

        h3 {
            margin-top: 0;
            color: #555;
            font-weight: normal;
        }

        input {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
        }

        button {
            padding: 8px 20px;
            margin-top: 10px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1>TechNovation</h1>
    <h3>Solutions</h3>

    <h2>Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input name="username" placeholder="Username">
        <input name="password" type="password" placeholder="Password">
        <button name="login">Login</button>
    </form>
    <p>
        New user?
        <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>
