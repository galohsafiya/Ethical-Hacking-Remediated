<?php
// Start session for session management remediation
session_start(); 
include 'db.php'; // Ensure db.php now defines $pdo
$message = '';
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Basic validation for "Broken Authentication" (OWASP A07)
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $message = "Login failed: Username and password are required.";
    } else {
        $u = $_POST['username'];
        $p = $_POST['password'];

        try {
            // 2. Use Prepared Statements to fix SQL Injection (OWASP A03)
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->execute([$u, $p]);
            $user = $stmt->fetch();

            if ($user) {
                // 3. Fix Broken Session Management (OWASP A01/A07)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Use htmlspecialchars to prevent XSS (OWASP A03)
                $message = "Login successful. Welcome " . htmlspecialchars($user['username']);
                $redirect = true; 
            } else {
                // 4. Generic error to prevent account enumeration
                $message = "Login failed: Invalid credentials.";
            }
        } catch (PDOException $e) {
            // 5. Information Disclosure remediation: Hide raw system errors
            $message = "A system error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TechNovation | Secure Login</title>
    <?php if ($redirect): ?>
    <meta http-equiv="refresh" content="2;url=user/dashboard.php">
    <?php endif; ?>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>TechNovation Solutions</h2>
    <p>Secure Enterprise Portal</p>

    <?php if ($message): ?>
        <div class="message <?php echo $redirect ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Authorized Access Only</button>
    </form>
    
    <p style="font-size: 0.8rem; color: #666; margin-top: 20px;">
        Security Notice: All login attempts are monitored by ModSecurity WAF.
    </p>
</div>

</body>
</html>
