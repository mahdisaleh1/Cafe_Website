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
    <title>Add Menu Item - Admin Barista Café</title>
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
                            <a class="nav-link click-scroll active" href="./menu.php">Menu</a>
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
        <section style="margin-top: 140px; margin-bottom: 100px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-12">
                        <h1 class="mb-4 text-center">Add New Menu Item</h1>
                        <div class="mb-4 text-end">
                            <a href="menu.php" class="btn btn-secondary">
                                <i class="bi bi-list"></i> See All Menu Items
                            </a>
                        </div>
                        <?php
                        // Handle form submission
                        $success = '';
                        $error = '';
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $name = trim($_POST['name']);
                            $category_id = intval($_POST['category_id']);
                            $description = trim($_POST['description']);
                            $price = floatval($_POST['price']);

                            if ($name === '' || $category_id <= 0 || $price <= 0) {
                                $error = "Please fill in all required fields and enter a valid price.";
                            } else {
                                $stmt = $con->prepare("INSERT INTO menu_items (name, category_id, description, price) VALUES (?, ?, ?, ?)");
                                $stmt->bind_param("sisd", $name, $category_id, $description, $price);
                                if ($stmt->execute()) {
                                    $success = "Menu item added successfully!";
                                } else {
                                    $error = "Failed to add menu item. Please try again.";
                                }
                                $stmt->close();
                            }
                        }
                        ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" class="p-4 rounded shadow-sm bg-white">
                            <div class="mb-3">
                                <label for="name" class="form-label">Menu Item Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $cat_result = $con->query("SELECT id, name FROM menu_categories ORDER BY name ASC");
                                    if ($cat_result && $cat_result->num_rows > 0) {
                                        while ($cat = $cat_result->fetch_assoc()) {
                                            echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" maxlength="255"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" required min="1" step="0.01">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success px-5">
                                    <i class="bi bi-plus-circle"></i> Add Menu Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
        <style>
            .bg-white {
                background: #fff;
            }

            .shadow-sm {
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            }

            .form-label {
                font-weight: 600;
            }

            .btn-success {
                background: var(--custom-btn-bg-color, #6c4f3d);
                border: none;
            }

            .btn-success:hover {
                background: #563927;
            }

            .btn-secondary {
                background: #888;
                border: none;
            }

            .btn-secondary:hover {
                background: #6c4f3d;
                color: #fff;
            }
        </style>

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

    .navbar-nav .nav-link.notactive {
        color: #fff !important;
    }
</style>

</html>