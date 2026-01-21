<?php
include 'db.php'; // Ensure db.php defines the PDO connection as $pdo
$error = '';

if (isset($_POST['register'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    // 1. FIX BROKEN AUTHENTICATION: Server-side validation for empty fields
    if (empty($u) || empty($p)) {
        $error = "Username and password cannot be empty.";
    } 
    // 2. PASSWORD HARDENING:, 
    // hashing is the industry standard remediation for "Weak Defaults."
    else {
        try {
            // Check if username already exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $checkStmt->execute([$u]);
            
            if ($checkStmt->fetch()) {
                $error = "Username already exists.";
            } else {
                // 3. FIX SQL INJECTION: Use Prepared Statements
                $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
                $stmt = $pdo->prepare($sql);

                // 4. DATA AT REST:Bc rypt Hashing
                $hashed_p = password_hash($p, PASSWORD_DEFAULT);
                $stmt->execute([$u, $hashed_p]);

                header("Location: index.php?msg=Registration successful");
                exit;
            }
        } catch (PDOException $e) {
            $error = "A system error occurred. Please try again later.";
        }
    }
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
