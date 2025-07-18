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
    <title>Menu Items - Admin Barista Café</title>
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
        <section style="margin-top: 140px;">
            <div class="container">
            <div class="row justify-content-center">
                <div>
                    <h1 class="mb-4">Menu Items</h1>
                    <p class="mb-5">View all menu items here. You can add, edit, or delete menu items.</p>
                    <!-- Add, Search & Category Filter Row Start -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                        <form method="get" class="mb-0 d-flex flex-row align-items-center" style="flex: 1; max-width: 600px; gap: 10px;">
                            <div class="input-group search-bar-menu" style="flex: 1;">
                                <input type="text" name="search" class="form-control" placeholder="Search menu items..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
                            </div>
                            <select name="category" class="form-select ms-2" style="max-width: 200px;">
                                <option value="">All Categories</option>
                                <?php
                                $catResult = $con->query("SELECT id, name FROM menu_categories ORDER BY name ASC");
                                $selectedCat = isset($_GET['category']) ? $_GET['category'] : '';
                                while ($cat = $catResult->fetch_assoc()) {
                                    $selected = ($selectedCat == $cat['id']) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($cat['id']) . "\" $selected>" . htmlspecialchars($cat['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </form>
                        <a href="add_menu_item.php" class="btn btn-success ms-md-3" style="white-space: nowrap;">
                            <i class="bi bi-plus-circle"></i> Add Menu Item
                        </a>
                    </div>
                    <!-- Add, Search & Category Filter Row End -->
                    <div class="table-responsive">
                        <table class="table table-bordered menu-categories-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                                $category = isset($_GET['category']) ? trim($_GET['category']) : '';
                                $sql = "SELECT m.id, m.name, m.description, m.price, m.status, c.name AS category_name 
                                    FROM menu_items m 
                                    LEFT JOIN menu_categories c ON m.category_id = c.id";
                                $params = [];
                                $types = '';
                                $where = [];
                                if ($search !== '') {
                                    $where[] = "(m.name LIKE ? OR m.description LIKE ? OR c.name LIKE ?)";
                                    $like = '%' . $search . '%';
                                    $params[] = $like;
                                    $params[] = $like;
                                    $params[] = $like;
                                    $types .= 'sss';
                                }
                                if ($category !== '') {
                                    $where[] = "m.category_id = ?";
                                    $params[] = $category;
                                    $types .= 'i';
                                }
                                if (count($where) > 0) {
                                    $sql .= " WHERE " . implode(' AND ', $where);
                                }
                                $sql .= " ORDER BY m.id";
                                if (count($params) > 0) {
                                    $stmt = $con->prepare($sql);
                                    $stmt->bind_param($types, ...$params);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                } else {
                                    $result = $con->query($sql);
                                }
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td>$" . number_format($row['price'], 2) . "</td>";
                                        echo "<td>";
                                        ?>
                                        <a href="./function/edit_menu_item.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm me-2">Edit</a>
                                        <?php if ($row['status'] === 'active'): ?>
                                        <a href="function/toggle_menu_item_status.php?id=<?php echo $row['id']; ?>&action=inactive" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to deactivate this menu item?');">Deactivate</a>
                                        <?php else: ?>
                                        <a href="function/toggle_menu_item_status.php?id=<?php echo $row['id']; ?>&action=activate" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to activate this menu item?');">Activate</a>
                                        <?php endif; ?>
                                        <?php
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No menu items found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <style>
            .search-bar-menu .form-control {
                border-radius: 30px 0 0 30px;
                border-right: none;
                box-shadow: none;
                padding-left: 20px;
                font-size: 1rem;
                background: #f8f9fa;
            }
            .search-bar-menu .btn {
                border-radius: 0 30px 30px 0;
                background: var(--custom-btn-bg-color, #6c4f3d);
                border: none;
                color: #fff;
                font-weight: 600;
                padding: 0 24px;
                transition: background 0.2s;
            }
            .search-bar-menu .btn:hover {
                background: #563927;
            }
            .form-select {
                border-radius: 30px;
                font-size: 1rem;
                background: #f8f9fa;
                border: 1px solid #ced4da;
                min-width: 150px;
            }
            @media (max-width: 576px) {
                .search-bar-menu {
                    max-width: 100% !important;
                }
                .d-flex.flex-md-row {
                    flex-direction: column !important;
                    align-items: stretch !important;
                }
                .btn.ms-md-3 {
                    margin-left: 0 !important;
                    margin-top: 10px;
                }
                .form-select.ms-2 {
                    margin-left: 0 !important;
                    margin-top: 10px;
                }
            }
        </style>
        <style>
            .menu-categories-table {
                background: #fff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
                margin-top: 30px;
            }

            .menu-categories-table th,
            .menu-categories-table td {
                vertical-align: middle;
                text-align: center;
            }

            .menu-categories-table th {
                background: var(--custom-btn-bg-color, #6c4f3d);
                color: #fff;
                font-weight: 600;
                border: none;
            }

            .menu-categories-table td {
                border-top: 1px solid #eee;
                font-size: 1rem;
            }

            .menu-categories-table tr:hover {
                background: #f8f9fa;
            }

            .menu-categories-table .btn {
                min-width: 70px;
            }

            .menu-categories-table .btn-primary {
                background: var(--custom-btn-bg-color, #6c4f3d);
                border: none;
            }

            .menu-categories-table .btn-primary:hover {
                background: #563927;
            }

            .menu-categories-table .btn-danger {
                background: #c0392b;
                border: none;
            }

            .menu-categories-table .btn-danger:hover {
                background: #922b21;
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
