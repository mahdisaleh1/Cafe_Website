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
    if ($role !== 'callcenter') {
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
    <title>Call Center Dashboard - Barista Café</title>
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
                            <a class="nav-link active" href="./callcenter_dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./menu.php">Menu Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./tables.php">Tables</a>
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
                        <h1>Welcome, <?php echo htmlspecialchars($admin['fullname']); ?>!</h1>
                        <p>Call Center Dashboard: Manage reservations and check tables/menu items.</p>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <!-- Reservations Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card shadow-sm border-0 text-center py-4 h-100 w-100">
                            <div class="card-body">
                                <i class="bi bi-calendar-check display-5 text-warning mb-2"></i>
                                <h5 class="card-title mb-1">Reservations</h5>
                                <?php
                                $res_count = $con->query("SELECT COUNT(*) FROM reservations")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $res_count; ?> Reservations</p>
                                <a href="reservations.php" class="btn btn-outline-warning btn-sm mt-2">View / Assign Table</a>
                                <a href="add_reservation.php" class="btn btn-outline-success btn-sm mt-2">Add Reservation</a>
                            </div>
                        </div>
                    </div>
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
                    <!-- Tables Card -->
                    <div class="col-lg-4 col-md-12 col-12">
                        <div class="card shadow-sm border-0 text-center py-4 h-100 w-100">
                            <div class="card-body">
                                <i class="bi bi-table display-5 text-info mb-2"></i>
                                <h5 class="card-title mb-1">Tables</h5>
                                <?php
                                $table_count = $con->query("SELECT COUNT(*) FROM tables")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $table_count; ?> Tables</p>
                                <a href="tables.php" class="btn btn-outline-info btn-sm mt-2">Check Availability</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer admin-footer" style="background: #6c4f3d; color: #fff; padding: 40px 0 20px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                        <h5 class="text-white mb-3"><i class="bi bi-shield-lock me-2"></i>Call Center Panel</h5>
                        <p class="mb-2">You are logged in as <strong><?php echo htmlspecialchars($admin['fullname']); ?></strong> (Call Center).</p>
                        <p class="mb-0">Add reservations, assign tables, and check menu items/tables from this dashboard.</p>
                    </div>
                    <div class="col-lg-3 col-12 mb-3 mb-lg-0">
                        <h6 class="text-white mb-3">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li><a href="./callcenter_dashboard.php" class="site-footer-link text-white">Dashboard</a></li>
                            <li><a href="./reservations.php" class="site-footer-link text-white">Reservations</a></li>
                            <li><a href="./tables.php" class="site-footer-link text-white">Tables</a></li>
                            <li><a href="./menu.php" class="site-footer-link text-white">Menu Items</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-12">
                        <h6 class="text-white mb-3">Support</h6>
                        <p class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:baristacafe@gmail.com" class="site-footer-link text-white">baristacafe@gmail.com</a></p>
                        <p class="mb-0"><i class="bi bi-box-arrow-right me-2"></i><a href="../logout.php" class="site-footer-link text-white">Logout</a></p>
                    </div>
                    <div class="col-12 mt-4">
                        <p class="copyright-text mb-0">
                            &copy; Barista Cafe Call Center <?php echo date('Y'); ?> - Design: <a rel="sponsored" href="http://mahdisaleh.ct.ws" target="_blank" class="text-white text-decoration-underline">Mahdi Saleh</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
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
