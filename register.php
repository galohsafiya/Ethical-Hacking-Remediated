<?php
include 'db.php'; 
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    
    // 1. Sanitize Username, preserve Password integrity
    $u = trim($_POST['username']); //trim username
    $p = $_POST['password'];

    if (empty($u) || empty($p)) {
        $error = "Username and password cannot be empty.";
    } else {
        try {
            // 2. Check for duplicate users (Prepared Statement)
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $checkStmt->execute([$u]);
            
            if ($checkStmt->fetch()) {
                $error = "Username already exists.";
            } else {
                // 3. Secure Storage: Bcrypt Hashing
                $hashed_p = password_hash($p, PASSWORD_DEFAULT);

                // 4. SQL Injection Prevention: PDO Prepared Statements
                $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
                $stmt = $pdo->prepare($sql);
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
    <title>TechNovation | Account Registration</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 50px; background-color: #f4f4f4; }
        .reg-box { background: white; padding: 20px; border-radius: 8px; width: 300px; margin: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input { width: 90%; padding: 8px; margin: 10px 0; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .error { color: red; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="reg-box">
    <h2>Register</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Create Account</button>
    </form>
    <p><a href="index.php">Back to Login</a></p>
</div>

</body>
</html>
