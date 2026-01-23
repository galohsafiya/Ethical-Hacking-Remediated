<?php

include '../db.php';

if (isset($_GET['user'])) {
    $user = $_GET['user'];

    try {
        /* * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
         * Replaced legacy mysqli_query with PDO Prepared Statements*/
        
        $stmtOrders = $pdo->prepare("DELETE FROM orders WHERE username = ?");
        $stmtOrders->execute([$user]);

        $stmtUser = $pdo->prepare("DELETE FROM users WHERE username = ?");
        $stmtUser->execute([$user]);

        header("Location: users.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        /* * REMEDIATION: INFORMATION DISCLOSURE PREVENTION
         * We catch database errors and prevent raw technical details 
         * from being displayed to the user.
         */
        error_log("Failed to delete user: " . $e->getMessage());
        header("Location: users.php?status=error");
        exit;
    }
} else {
    header("Location: users.php");
    exit;
}
