<?php
// Start session for session management remediation
session_start(); 
include 'db.php'; // Ensure db.php now defines $pdo
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Basic validation for "Broken Authentication"
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $message = "Login failed: Username and password are required.";
    } else {
        $u = $_POST['username'];
        $p = $_POST['password'];

        try {
            // 2. Use Prepared Statements to fix SQL Injection
            // We use place holders (?) instead of variables in the string
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->execute([$u, $p]);
            $user = $stmt->fetch();

            if ($user) {
                // 3. Fix Broken Session Management by setting session variables properly 
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                $message = "Login successful. Welcome " . htmlspecialchars($user['username']);
            } else {
                $message = "Login failed: Invalid credentials.";
            }
        } catch (PDOException $e) {
            // Do not echo the actual error $e->getMessage() in production 
            // to avoid "Information Disclosure"
            $message = "A system error occurred. Please try again later.";
        }
    }
}
?>
