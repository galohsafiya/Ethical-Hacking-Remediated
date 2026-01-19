<?php
include '../db.php';
include '../navbar.php';

/* Overview metrics */
$total_users = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role != 'admin'")
)['total'];

$total_products = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products")
)['total'];

$total_orders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders")
)['total'];

$total_revenue = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total) AS revenue FROM orders")
)['revenue'] ?? 0;

/* Most sold product */
$most_sold = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT p.name, SUM(oi.quantity) AS sold
         FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         GROUP BY oi.product_id
         ORDER BY sold DESC
         LIMIT 1"
    )
);

/* Top customers */
$top_customers = mysqli_query(
    $conn,
    "SELECT username, SUM(total) AS spent
     FROM orders
     GROUP BY username
     ORDER BY spent DESC
     LIMIT 3"
);

/* Recent orders */
$recent_orders = mysqli_query(
    $conn,
    "SELECT * FROM orders ORDER BY id DESC LIMIT 5"
);
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
        margin-bottom: 20px;
    }

    h2 {
        margin-top: 40px;
    }

    .analytics {
        background: #fafafa;
        padding: 20px;
        border-radius: 5px;
    }
</style>

<div class="dashboard">

    <h1>Admin Dashboard</h1>

    <!-- Overview widgets -->
    <div class="widgets">
        <div class="widget">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>

        <div class="widget">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>

        <div class="widget">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>

        <div class="widget">
            <h3>Total Revenue</h3>
            <p>RM <?php echo $total_revenue; ?></p>
        </div>
    </div>

    <!-- Analytics section -->
    <h2>Analytics</h2>

    <div class="analytics">

        <p>
            <strong>Most Sold Product:</strong>
            <?php
            if ($most_sold) {
                echo $most_sold['name'] . " ({$most_sold['sold']} units sold)";
            } else {
                echo "No sales data";
            }
            ?>
        </p>

        <p><strong>Top Customers:</strong></p>
        <ul>
            <?php while ($c = mysqli_fetch_assoc($top_customers)): ?>
                <li>
                    <?php echo $c['username']; ?> —
                    RM <?php echo $c['spent']; ?>
                </li>
            <?php endwhile; ?>
        </ul>

        <h3>Recent Orders</h3>

        <?php while ($o = mysqli_fetch_assoc($recent_orders)): ?>
            <p>
                Order #<?php echo $o['id']; ?> |
                User: <?php echo $o['username']; ?> |
                RM <?php echo $o['total']; ?>
            </p>
        <?php endwhile; ?>

    </div>

</div>
