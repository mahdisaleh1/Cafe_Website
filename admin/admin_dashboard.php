<?php
include '../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
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
    }
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
    <title>Admin Dashboard - Barista Café</title>
    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap"
        rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/vegas.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/icon.png" type="image/x-icon"> <!--ICON-->
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
                            <a class="nav-link click-scroll active" href="./admin_dashboard.php">Home</a>
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
                            <a class="nav-link click-scroll" href="./tables.php">Tables</a>
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
                <div class="row mb-5">
                    <div class="col-lg-12 text-center">
                        <h1>Welcome, <?php echo htmlspecialchars($admin['fullname']); ?>!</h1>
                        <p>Here's a quick overview of your café's activity</p>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <!-- Dashboard Cards -->
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-list-ul display-5 text-primary mb-2"></i>
                                <h5 class="card-title mb-1">Menu Categories</h5>
                                <?php
                                $cat_count = $con->query("SELECT COUNT(*) FROM menu_categories")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $cat_count; ?> Categories</p>
                                <a href="menu_categories.php" class="btn btn-outline-primary btn-sm mt-2">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-cup-straw display-5 text-success mb-2"></i>
                                <h5 class="card-title mb-1">Menu Items</h5>
                                <?php
                                $item_count = $con->query("SELECT COUNT(*) FROM menu_items")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $item_count; ?> Items</p>
                                <a href="menu.php" class="btn btn-outline-success btn-sm mt-2">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-calendar-check display-5 text-warning mb-2"></i>
                                <h5 class="card-title mb-1">Reservations</h5>
                                <?php
                                $res_count = $con->query("SELECT COUNT(*) FROM reservations")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $res_count; ?> Reservations</p>
                                <a href="reservations.php" class="btn btn-outline-warning btn-sm mt-2">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-people display-5 text-danger mb-2"></i>
                                <h5 class="card-title mb-1">Users</h5>
                                <?php
                                $user_count = $con->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $user_count; ?> Users</p>
                                <a href="users.php" class="btn btn-outline-danger btn-sm mt-2">Manage</a>
                            </div>
                        </div>
                    </div>
                    <!-- New Cards -->
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-table display-5 text-info mb-2"></i>
                                <h5 class="card-title mb-1">Tables</h5>
                                <?php
                                $table_count = $con->query("SELECT COUNT(*) FROM tables")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $table_count; ?> Tables</p>
                                <a href="tables.php" class="btn btn-outline-info btn-sm mt-2">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-chat-dots display-5 text-secondary mb-2"></i>
                                <h5 class="card-title mb-1">Feedback</h5>
                                <?php
                                $feedback_count = $con->query("SHOW TABLES LIKE 'feedback'")->num_rows ? $con->query("SELECT COUNT(*) FROM feedback")->fetch_row()[0] : 0;
                                ?>
                                <p class="card-text"><?php echo $feedback_count; ?> Feedbacks</p>
                                <a href="feedback.php" class="btn btn-outline-secondary btn-sm mt-2">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-clock-history display-5 text-dark mb-2"></i>
                                <h5 class="card-title mb-1">Today's Reservations</h5>
                                <?php
                                $today = date('Y-m-d');
                                $today_res_count = $con->query("SELECT COUNT(*) FROM reservations WHERE DATE(reservation_date) = '$today'")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $today_res_count; ?> Today</p>
                                <a href="reservations.php" class="btn btn-outline-dark btn-sm mt-2">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm border-0 text-center py-4 h-100">
                            <div class="card-body">
                                <i class="bi bi-person-check display-5 text-primary mb-2"></i>
                                <h5 class="card-title mb-1">Admins</h5>
                                <?php
                                $admin_count = $con->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
                                ?>
                                <p class="card-text"><?php echo $admin_count; ?> Admins</p>
                                <a href="users.php" class="btn btn-outline-primary btn-sm mt-2">View</a>
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

    <!-- JAVASCRIPT FILES -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.sticky.js"></script>
    <script src="../js/click-scroll.js"></script>
    <script src="../js/vegas.min.js"></script>
    <script src="../js/custom.js"></script>

</body>
<style>
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
        /* center horizontally and add vertical space */
        display: block;
        /* required for auto margins to take effect */
        text-align: center;
    }
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e3c9b6 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
</style>

</html>