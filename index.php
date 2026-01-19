<?php
session_start();
include 'db.php'; // Using your new PDO connection

$error = '';

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    try {
        // 1. FIXED: Using PDO Prepared Statements to prevent SQL Injection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$u, $p]);
        $user = $stmt->fetch();

        if ($user) {
            // 2. FIXED: Proper Session Management
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // 3. FIXED: Secure Redirection based on Role
            if ($user['role'] === 'admin') {
                header("Location: admin/admin.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit;
        } else {
            // 4. FIXED: Generic Error Message (Prevents Username Enumeration)
            $error = "Invalid login credentials.";
        }
    } catch (PDOException $e) {
        // 5. FIXED: Preventing Information Disclosure
        $error = "A system error occurred. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TechNovation Solutions | Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .login-box { width: 350px; margin: 120px auto; padding: 25px; background: white; border: 1px solid #ccc; text-align: center; }
        h1 { margin-bottom: 5px; }
        h3 { margin-top: 0; color: #555; font-weight: normal; }
        input { width: 90%; padding: 8px; margin: 8px 0; }
        button { padding: 8px 20px; margin-top: 10px; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="login-box">
    <h1>TechNovation</h1>
    <h3>Solutions</h3>
    <h2>Login (Hardened)</h2>

    <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button name="login" type="submit">Login</button>
    </form>
    <p>New user? <a href="register.php">Register here</a></p>
</div>

</body>
</html>
