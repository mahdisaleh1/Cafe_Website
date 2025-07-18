<?php
include '../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
} else {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $role = $admin['role'];
    if ($role !== 'admin') {
        header("Location: ../login_auth.php");
        exit();
    }
}

// Fetch all tables for dropdown
$tables_query = "SELECT id, table_number FROM tables ORDER BY table_number ASC";
$tables_result = $con->query($tables_query);

// Handle search
$search_table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : '';
$search_session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : '';

$sessions = [];
$orders = [];
$show_orders = false;

// If table is selected, fetch sessions for that table
if ($search_table_id) {
    $sessions_query = "SELECT ts.*, u.fullname 
                       FROM table_sessions ts
                       LEFT JOIN users u ON ts.created_by = u.id
                       WHERE ts.table_id = ?
                       ORDER BY ts.started_at DESC";
    $stmt = $con->prepare($sessions_query);
    $stmt->bind_param("i", $search_table_id);
    $stmt->execute();
    $sessions_result = $stmt->get_result();
    while ($row = $sessions_result->fetch_assoc()) {
        $sessions[] = $row;
    }
}

// If session is selected, fetch orders for that session
if ($search_session_id) {
    // Get all order IDs for this session
    $orders_query = "SELECT id FROM table_orders WHERE session_id = ?";
    $stmt = $con->prepare($orders_query);
    $stmt->bind_param("i", $search_session_id);
    $stmt->execute();
    $orders_result = $stmt->get_result();
    $order_ids = [];
    while ($row = $orders_result->fetch_assoc()) {
        $order_ids[] = $row['id'];
    }

    if (!empty($order_ids)) {
        // Prepare placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($order_ids), '?'));
        $types = str_repeat('i', count($order_ids));

        // Build query to get order items with menu item info
        $sql = "SELECT oi.*, too.created_at, m.name AS item_name, m.price 
                FROM order_items oi
                LEFT JOIN menu_items m ON oi.menu_item_id = m.id
                LEFT JOIN table_orders too ON oi.order_id = too.id
                WHERE oi.order_id IN ($placeholders)
                ORDER BY oi.id ASC"; 
        $stmt = $con->prepare($sql);
        $stmt->bind_param($types, ...$order_ids);
        $stmt->execute();
        $items_result = $stmt->get_result();
        while ($row = $items_result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    $show_orders = true;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="barista cafe is a free Bootstrap 5 HTML template for coffee shops, cafes, and restaurants.">
    <meta name="keywords" content="barista, cafe, coffee, restaurant, bootstrap, html, template">
    <meta name="author" content="Mahdi Saleh">
    <title>Table History - Admin Barista Café</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/vegas.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/icon.png" type="image/x-icon">
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
    <main>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="../images/coffee-beans.png" class="navbar-brand-image img-fluid" alt="Barista Cafe Template">
                    Barista Café
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link click-scroll notactive" href="./admin_dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./menu_categories.php">Menu Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./menu.php">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./reservations.php">Reservations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll active" href="./tables.php">Tables</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./users.php">Users</a>
                        </li>
                    </ul>
                    <div class="ms-lg-3">
                        <a class="btn custom-btn custom-border-btn" href="../logout.php">
                            LOGOUT
                            <i class="bi-arrow-up-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <section style="min-height: 60vh; margin-top: 150px;">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Table History</h2>
                    <a href="./tables.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Tables
                    </a>
                </div>
                <form class="row g-3 mb-4" method="get" action="">
                    <div class="col-md-4">
                        <label for="table_id" class="form-label">Table Number</label>
                        <select class="form-select" id="table_id" name="table_id" onchange="this.form.submit()">
                            <option value="">Select Table</option>
                            <?php
                            // Reset pointer for tables_result
                            $tables_result->data_seek(0);
                            while ($row = $tables_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" <?php if ($search_table_id == $row['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['table_number']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- No date filter, only table selection -->
                    <div class="col-md-4 d-flex align-items-end">
                        <?php if ($search_table_id): ?>
                            <a href="table_history.php" class="btn btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if (!$search_table_id): ?>
                    <div class="alert alert-info text-center">Please select a table number to view its session history.</div>
                <?php elseif (!$show_orders): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle bg-white">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Changed By</th>
                                    <th>Started At</th>
                                    <th>Ended At</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($sessions) > 0): $i = 1; foreach ($sessions as $row): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        
                                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['started_at']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ended_at']); ?></td>
                                        <td>
                                            <a href="?table_id=<?php echo $search_table_id; ?>&session_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                                View Orders
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No sessions found for this table.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($show_orders): ?>
                    <div class="mb-3">
                        <a href="table_history.php?table_id=<?php echo $search_table_id; ?>" class="btn btn-secondary mb-2">
                            <i class="bi bi-arrow-left"></i> Back to Sessions
                        </a>
                        <h4>Session Orders</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle bg-white">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <!--<th>Notes</th>-->
                                    <th>Ordered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($orders) > 0): $i = 1; foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                                        <td>$<?php echo number_format($order['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                        <!--<td><?php echo htmlspecialchars($order['notes']); ?></td>-->
                                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No orders found for this session.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <footer class="site-footer admin-footer" style="background: #6c4f3d; color: #fff; padding: 40px 0 20px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                        <h5 class="text-white mb-3"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h5>
                        <p class="mb-2">You are logged in as <strong><?php echo htmlspecialchars($admin['fullname']); ?></strong> (Admin).</p>
                        <p class="mb-0">Manage menu categories, menu items, reservations, tables, and users from this dashboard.</p>
                    </div>
                    <div class="col-lg-3 col-12 mb-3 mb-lg-0">
                        <h6 class="text-white mb-3">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li><a href="./admin_dashboard.php" class="site-footer-link text-white">Dashboard</a></li>
                            <li><a href="./menu_categories.php" class="site-footer-link text-white">Menu Categories</a></li>
                            <li><a href="./menu.php" class="site-footer-link text-white">Menu</a></li>
                            <li><a href="./users.php" class="site-footer-link text-white">Users</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-12">
                        <h6 class="text-white mb-3">Support</h6>
                        <p class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:baristacafe@gmail.com" class="site-footer-link text-white">baristacafe@gmail.com</a></p>
                        <p class="mb-0"><i class="bi bi-box-arrow-right me-2"></i><a href="../logout.php" class="site-footer-link text-white">Logout</a></p>
                    </div>
                    <div class="col-12 mt-4">
                        <p class="copyright-text mb-0">
                            &copy; Barista Cafe Admin <?php echo date('Y'); ?> - Design: <a rel="sponsored" href="http://mahdisaleh.ct.ws" target="_blank" class="text-white text-decoration-underline">Mahdi Saleh</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </main>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.sticky.js"></script>
    <script src="../js/click-scroll.js"></script>
    <script src="../js/vegas.min.js"></script>
    <script src="../js/custom.js"></script>
</body>
<style>
    .navbar-nav .nav-link.notactive {
        color: #fff !important;
    }
    .menubtn {
        background: var(--custom-btn-bg-color);
        border: 2px solid transparent;
        border-radius: var(--border-radius-large);
        color: var(--white-color);
        font-size: var(--btn-font-size);
        font-weight: var(--font-weight-bold);
        line-height: normal;
        transition: all 0.3s;
        padding: 12px 28px;
        margin: 20px auto;
        display: block;
        text-align: center;
    }
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e3c9b6 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
</style>
</html>
