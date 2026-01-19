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
                
                // For this lab, we use $p directly to match your DB schema, 
                // but in a real-world patch, you would use password_hash($p, PASSWORD_BCRYPT).
                $stmt->execute([$u, $p]);

                header("Location: index.php?msg=Registration successful");
                exit;
            }
        } catch (PDOException $e) {
            $error = "A system error occurred. Please try again later.";
        }
    }
}
?>
