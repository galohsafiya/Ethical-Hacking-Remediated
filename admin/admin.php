<?php
session_start();
include '../db.php';
include '../navbar.php';

// 1. REMEDIATION: Broken Access Control (A01:2021)
// If not logged in OR not an admin, kick them out immediately
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

try {
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
    $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    
    $revenue_stmt = $pdo->query("SELECT SUM(total) FROM orders");
    $total_revenue = $revenue_stmt->fetchColumn() ?? 0;

    $most_sold = $pdo->query("
        SELECT p.name, SUM(oi.quantity) AS sold
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY oi.product_id
        ORDER BY sold DESC
        LIMIT 1
    ")->fetch();

    $top_customers = $pdo->query("
        SELECT username, SUM(total) AS spent
        FROM orders
        GROUP BY username
        ORDER BY spent DESC
        LIMIT 3
    ")->fetchAll();

    $recent_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

} catch (PDOException $e) {
    // Information Disclosure Prevention: Log error internally, show generic message
    die("Data retrieval error. Please contact the system administrator.");
}
?>

<style>
    .dashboard { padding: 20px; font-family: sans-serif; }
    .widgets { display: flex; gap: 20px; margin-bottom: 30px; }
    .widget { background: #f4f4f4; padding: 20px; flex: 1; border-radius: 5px; text-align: center; border: 1px solid #ddd; }
    .analytics { background: #fafafa; padding: 20px; border-radius: 5px; border: 1px solid #eee; }
    .error-notice { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
</style>

<div class="dashboard">
    <h1>Admin Dashboard (Secured)</h1>

    <div class="widgets">
        <div class="widget">
            <h3>Total Users</h3>
            <p><?php echo htmlspecialchars($total_users); ?></p>
        </div>
        <div class="widget">
            <h3>Total Products</h3>
            <p><?php echo htmlspecialchars($total_products); ?></p>
        </div>
        <div class="widget">
            <h3>Total Orders</h3>
            <p><?php echo htmlspecialchars($total_orders); ?></p>
        </div>
        <div class="widget">
            <h3>Total Revenue</h3>
            <p>RM <?php echo htmlspecialchars(number_format($total_revenue, 2)); ?></p>
        </div>
    </div>

    <div class="analytics">
        <h2>Business Intelligence</h2>
        <p>
            <strong>Most Sold Product:</strong>
            <?php echo $most_sold ? htmlspecialchars($most_sold['name']) . " ({$most_sold['sold']} units)" : "No data"; ?>
        </p>

        <h3>Top Customers</h3>
        <ul>
            <?php foreach ($top_customers as $c): ?>
                <li><?php echo htmlspecialchars($c['username']); ?> — RM <?php echo htmlspecialchars(number_format($c['spent'], 2)); ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Recent Orders</h3>
        <?php foreach ($recent_orders as $o): ?>
            <p>
                Order #<?php echo htmlspecialchars($o['id']); ?> | 
                User: <?php echo htmlspecialchars($o['username']); ?> | 
                <strong>RM <?php echo htmlspecialchars(number_format($o['total'], 2)); ?></strong>
            </p>
        <?php endforeach; ?>
    </div>
</div>
