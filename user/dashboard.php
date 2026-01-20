<?php
session_start();
include '../db.php'; // This must define $pdo
include '../navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];

try {
    // REMEDIATION: Use PDO Prepared Statements instead of mysqli_query
    // This prevents SQL Injection in the dashboard metrics
    
    // 1. Get total orders
    $stmt1 = $pdo->prepare("SELECT COUNT(*) AS total FROM orders WHERE username = ?");
    $stmt1->execute([$user]);
    $total_orders = $stmt1->fetch()['total'];

    // 2. Get last order
    $stmt2 = $pdo->prepare("SELECT total FROM orders WHERE username = ? ORDER BY id DESC LIMIT 1");
    $stmt2->execute([$user]);
    $last_order = $stmt2->fetch();

} catch (PDOException $e) {
    // Silently handle error or log it - prevents Information Disclosure
    $total_orders = 0;
    $last_order = null;
}
?>

<style>
    .dashboard {
        padding: 20px;
    }

    .widgets {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .widget {
        background: #f4f4f4;
        padding: 20px;
        flex: 1;
        border-radius: 5px;
        text-align: center;
    }

    h1 {
        margin-bottom: 10px;
    }

    h2 {
        margin-top: 40px;
    }

    .actions {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
    }

    .action {
        background: #fafafa;
        padding: 20px;
        flex: 1;
        border-radius: 5px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .action a {
        text-decoration: none;
        font-weight: bold;
    }

    .info {
        background: #fafafa;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
</style>

<div class="dashboard">

    <h1>Hi, <?php echo htmlspecialchars($user); ?> 👋</h1>
    <p>Welcome back to <strong>TechNovation</strong></p>

    <!-- User widgets -->
    <div class="widgets">
        <div class="widget">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>

        <div class="widget">
            <h3>Last Order Total</h3>
            <p>
                RM <?php echo $last_order ? $last_order['total'] : '0'; ?>
            </p>
        </div>

        <div class="widget">
            <h3>Account Status</h3>
            <p>Active</p>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="actions">
        <div class="action">
            <a href="../products.php">🛒 Browse Products</a>
        </div>

        <div class="action">
            <a href="../cart.php">🧺 View Cart</a>
        </div>

        <div class="action">
            <a href="orders.php">📦 My Orders</a>
        </div>

        <div class="action">
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>

    <!-- Account info -->
    <h2>Account Information</h2>

    <div class="info">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user); ?></p>
        <p><strong>Role:</strong> User</p>
    </div>

</div>
