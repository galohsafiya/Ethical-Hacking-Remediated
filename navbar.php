<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    .navbar {
        background: #333;
        padding: 10px;
    }

    .navbar a {
        color: white;
        margin-right: 15px;
        text-decoration: none;
        font-weight: bold;
    }

    .navbar span {
        color: #ccc;
        margin-right: 15px;
    }
</style>

<div class="navbar">

<?php if (isset($_SESSION['user'])): ?>

    <span>Hi, <?php echo $_SESSION['user']; ?></span>

    <?php if ($_SESSION['role'] === 'admin'): ?>

        <a href="/technovation/admin/admin.php">Admin Panel</a>
        <a href="/technovation/admin/products.php">Products</a>
        <a href="/technovation/admin/users.php">Users</a>
        <a href="/technovation/products.php">View Site</a>
        <a href="/technovation/logout.php">Logout</a>

    <?php else: ?>

        <a href="/technovation/user/dashboard.php">Dashboard</a>
        <a href="/technovation/products.php">Products</a>
        <a href="/technovation/cart.php">Cart</a>
        <a href="/technovation/user/orders.php">My Orders</a>
        <a href="/technovation/logout.php">Logout</a>

    <?php endif; ?>

<?php else: ?>

    <a href="/technovation/index.php">Login</a>

<?php endif; ?>

</div>
