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
    $user = $result->fetch_assoc();
    $role = $user['role'];
    if ($role !== 'waiter') {
        header("Location: ../login_auth.php");
        exit();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Waiter Dashboard - Barista Café</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/icon.png" type="image/x-icon">
</head>

<body>
    <main>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
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
                            <a class="nav-link active" href="./waiter_dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./menu.php">Menu Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./tables.php">Tables & Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./reservations.php">Reservations</a>
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
        <section style="min-height: 60vh;">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-12 text-center">
                        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
                        <p>Waiter Dashboard: Manage tables, orders, sessions, and reservations.</p>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <!-- Menu Items Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card shadow-sm border-0 text-center py-4 h-100 w-100">
                            <div class="card-body">
                                <i class="bi bi-cup-straw display-5 text-success mb-2"></i>
                                <h5 class="card-title mb-1">Menu Items</h5>
                                <?php
                                $item_count = $con->query("SELECT COUNT(*) FROM menu_items")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $item_count; ?> Items</p>
                                <a href="menu.php" class="btn btn-outline-success btn-sm mt-2">Check Menu</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tables & Orders Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card shadow-sm border-0 text-center py-4 h-100 w-100">
                            <div class="card-body">
                                <i class="bi bi-table display-5 text-info mb-2"></i>
                                <h5 class="card-title mb-1">Tables & Orders</h5>
                                <?php
                                $table_count = $con->query("SELECT COUNT(*) FROM tables")->fetch_row()[0];
                                $order_count = $con->query("SELECT COUNT(*) FROM table_orders WHERE status != 'paid'")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $table_count; ?> Tables, <?php echo $order_count; ?> Active Orders</p>
                                <a href="tables.php" class="btn btn-outline-info btn-sm mt-2">Check Tables & Orders</a>
                                <a href="make_order.php" class="btn btn-outline-success btn-sm mt-2">Make Table Order</a>
                                <a href="order_paid.php" class="btn btn-outline-primary btn-sm mt-2">Mark Order Paid</a>
                            </div>
                        </div>
                    </div>
                    <!-- Reservations Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card shadow-sm border-0 text-center py-4 h-100 w-100">
                            <div class="card-body">
                                <i class="bi bi-calendar-check display-5 text-secondary mb-2"></i>
                                <h5 class="card-title mb-1">Reservations</h5>
                                <?php
                                $res_count = $con->query("SELECT COUNT(*) FROM reservations")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $res_count; ?> Reservations</p>
                                <a href="reservations.php" class="btn btn-outline-secondary btn-sm mt-2">Check Reservations</a>
                                <a href="reservation_status.php?status=completed" class="btn btn-outline-success btn-sm mt-2">Mark as Completed</a>
                                <a href="reservation_status.php?status=cancelled" class="btn btn-outline-danger btn-sm mt-2">Mark as Cancelled</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <!-- Quick Actions Card -->
                    <div class="col-lg-12 col-md-6 col-12">
                        <div class="card shadow-sm border-0 text-center py-4 h-100 w-100">
                            <div class="card-body">
                                <i class="bi bi-lightning-charge display-5 text-primary mb-2"></i>
                                <h5 class="card-title mb-1">Quick Actions</h5>
                                <p class="card-text">Create table session, make order, mark order paid, update reservation status.</p>
                                <a href="table_sessions.php?action=create" class="btn btn-outline-warning btn-sm mt-2">Create Table Session</a>
                                <a href="make_order.php" class="btn btn-outline-success btn-sm mt-2">Make Order</a>
                                <a href="order_paid.php" class="btn btn-outline-primary btn-sm mt-2">Order Paid</a>
                                <a href="reservation_status.php" class="btn btn-outline-secondary btn-sm mt-2">Update Reservation</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="site-footer admin-footer" style="background: #6c4f3d; color: #fff; padding: 40px 0 20px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                        <h5 class="text-white mb-3"><i class="bi bi-shield-lock me-2"></i>Waiter Panel</h5>
                        <p class="mb-2">You are logged in as <strong><?php echo htmlspecialchars($user['fullname']); ?></strong> (Waiter).</p>
                        <p class="mb-0">Manage tables, orders, sessions, and reservations from this dashboard.</p>
                    </div>
                    <div class="col-lg-3 col-12 mb-3 mb-lg-0">
                        <h6 class="text-white mb-3">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li><a href="./waiter_dashboard.php" class="site-footer-link text-white">Dashboard</a></li>
                            <li><a href="./menu.php" class="site-footer-link text-white">Menu Items</a></li>
                            <li><a href="./tables.php" class="site-footer-link text-white">Tables & Orders</a></li>
                            <li><a href="./table_sessions.php" class="site-footer-link text-white">Table Sessions</a></li>
                            <li><a href="./reservations.php" class="site-footer-link text-white">Reservations</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-12">
                        <h6 class="text-white mb-3">Support</h6>
                        <p class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:baristacafe@gmail.com" class="site-footer-link text-white">baristacafe@gmail.com</a></p>
                        <p class="mb-0"><i class="bi bi-box-arrow-right me-2"></i><a href="../logout.php" class="site-footer-link text-white">Logout</a></p>
                    </div>
                    <div class="col-12 mt-4">
                        <p class="copyright-text mb-0">
                            &copy; Barista Cafe Waiter <?php echo date('Y'); ?> - Design: <a rel="sponsored" href="http://mahdisaleh.ct.ws" target="_blank" class="text-white text-decoration-underline">Mahdi Saleh</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
        <!-- End Footer -->
    </main>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/custom.js"></script>
</body>
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e3c9b6 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
</style>
</html>
