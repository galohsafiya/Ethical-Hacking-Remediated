<?php
// Initialize session only if one does not already exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    .navbar {
        background: #2c3e50; /* Updated to a professional enterprise theme */
        padding: 12px 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar a {
        color: #ecf0f1;
        margin-right: 20px;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s;
    }

    .navbar a:hover {
        color: #3498db;
    }

    .navbar span {
        color: #bdc3c7;
        margin-right: 20px;
        font-style: italic;
    }
    
    /* REMEDIATION: Visual indicator of Role-Based Access Control (RBAC) */
    .role-tag {
        font-size: 0.75rem;
        background: #e74c3c;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 5px;
    }
</style>

<nav class="navbar">

<?php if (isset($_SESSION['user'])): ?>

    <?php 
    /** * REMEDIATION: CROSS-SITE SCRIPTING (XSS) PROTECTION (OWASP A03:2021)
     * We apply htmlspecialchars() to the session username. 
     * This prevents any malicious scripts embedded in the username from 
     * executing in the browser context.
     */
    $safe_user = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); 
    ?>

    <span>Logged in as: <strong><?php echo $safe_user; ?></strong>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <small class="role-tag">Admin</small>
        <?php endif; ?>
    </span>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="/technovation/admin/admin.php">Admin Panel</a>
        <a href="/technovation/admin/products.php">Product Mgmt</a>
        <a href="/technovation/admin/users.php">User Mgmt</a>
        <a href="/technovation/products.php">Shop View</a>
        <a href="/technovation/logout.php" style="color:#e74c3c;">Secure Logout</a>

    <?php else: ?>
        <a href="/technovation/user/dashboard.php">Dashboard</a>
        <a href="/technovation/products.php">Products</a>
        <a href="/technovation/cart.php">Shopping Cart</a>
        <a href="/technovation/user/orders.php">My Orders</a>
        <a href="/technovation/logout.php" style="color:#e74c3c;">Secure Logout</a>

    <?php endif; ?>

<?php else: ?>

    <a href="/technovation/index.php">Portal Login</a>

<?php endif; ?>

</nav>
