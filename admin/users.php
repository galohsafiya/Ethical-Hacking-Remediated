<?php

include '../db.php';
include '../navbar.php';
?>

<h1>User Management (Authorized Personnel Only)</h1>

<hr>

<h2>System Accounts</h2>

<?php
try {
    /** * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021) 
     * Replaced legacy mysqli_query with a PDO query. 
     */
    $stmt = $pdo->query("SELECT id, username, role FROM users WHERE role != 'admin'");

    // Fetching user data securely into an associative array [cite: 1158]
    while ($u = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        /** * REMEDIATION: OUTPUT ENCODING FOR XSS PROTECTION (OWASP A03:2021)
         * We apply htmlspecialchars() to all user-generated content retrieved 
         * from the database.
         */
        $safe_id = htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8');
        $safe_user = htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8');
        $safe_role = htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8');

        echo "<div class='user-entry' style='margin-bottom: 10px; padding: 5px; border-bottom: 1px solid #ddd;'>";
        echo "<strong>ID:</strong> " . $safe_id . " | ";
        echo "<strong>Username:</strong> " . $safe_user . " | ";
        echo "<strong>Role:</strong> " . $safe_role . " ";
        echo "<a href='user_orders.php?user=" . urlencode($u['username']) . "'>[View Orders]</a> ";
        echo "<a href='delete_user.php?user=" . urlencode($u['username']) . "' onclick=\"return confirm('Confirm deletion?')\" style='color:red;'>[Delete User]</a>";
        echo "</div>";
    }

} catch (PDOException $e) {
    /** * REMEDIATION: INFORMATION DISCLOSURE PREVENTION 
     * Technical errors are caught and logged server-side. The end-user 
     * sees only a generic error message, protecting the database schema 
     * from reconnaissance attempts.
     */
    error_log("Database Error in admin/users.php: " . $e->getMessage());
    echo "<p class='error'>System error: Unable to retrieve user list. Please verify database connectivity.</p>";
}
?>
