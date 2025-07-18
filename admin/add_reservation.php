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
    <title>Add Reservation - Admin Barista Café</title>
    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap"
        rel="stylesheet">
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
                            <a class="nav-link click-scroll " href="./menu.php">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll active" href="./reservations.php">Reservations</a>
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
        <section style="margin-top: 140px; margin-bottom: 50px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <div class="card shadow-sm p-4">
                            <h2 class="mb-3"><i class="bi bi-tools"></i> Page Under Construction</h2>
                            <p class="lead mb-4">This page is currently under construction and will be available soon.</p>
                            <img src="../images/under_construction.png" alt="Under Construction" style="max-width:400px; text-align: center;" class="mb-3">
                            <a href="./admin_dashboard.php" class="btn btn-primary mt-3">
                                <i class="bi bi-arrow-left"></i> Back to Home Page
                            </a>
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
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e3c9b6 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
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

    .navbar-nav .nav-link.notactive {
        color: #fff !important;
    }
</style>

</html>