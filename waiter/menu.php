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
// Fetch menu items
$menu_items = [];
$result = $con->query("
                SELECT mi.*, cm.name AS category
                FROM menu_items mi
                JOIN menu_categories cm ON mi.category_id = cm.id
                ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menu_items[] = $row;
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
                    <div class="col-lg-12 text-center mb-4">
                        <h1>Menu Items</h1>
                        <p>Check all available menu items below.</p>
                    </div>
                    <div class="col-lg-10 mx-auto mb-4">
                        <form class="row g-3 align-items-center justify-content-center bg-white shadow-sm rounded p-3" method="get" action="">
                            <div class="col-md-5">
                                <label for="search" class="form-label visually-hidden">Search by name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Search menu item..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label visually-hidden">Category</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-list"></i></span>
                                    <select name="category" id="category" class="form-select">
                                        <option value="">All Categories</option>
                                        <?php
                                        // Fetch categories for select
                                        $cat_result = $con->query("SELECT id, name FROM menu_categories ORDER BY name ASC");
                                        while ($cat = $cat_result->fetch_assoc()):
                                            $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <a href="menu.php" class="btn btn-outline-secondary flex-fill">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    <?php
                    // Handle search and category filter
                    $where = [];
                    $params = [];
                    $types = '';

                    if (!empty($_GET['search'])) {
                        $where[] = "mi.name LIKE ?";
                        $params[] = '%' . $_GET['search'] . '%';
                        $types .= 's';
                    }
                    if (!empty($_GET['category'])) {
                        $where[] = "mi.category_id = ?";
                        $params[] = $_GET['category'];
                        $types .= 'i';
                    }

                    $query = "
    SELECT mi.*, cm.name AS category
    FROM menu_items mi
    JOIN menu_categories cm ON mi.category_id = cm.id
";
                    if ($where) {
                        $query .= " WHERE " . implode(' AND ', $where);
                    }
                    $query .= " ORDER BY mi.id DESC";

                    $menu_items = [];
                    $stmt = $con->prepare($query);
                    if ($params) {
                        $stmt->bind_param($types, ...$params);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $menu_items[] = $row;
                    }
                    ?>
                </div>
                <div class="row">
                    <?php if (count($menu_items) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menu_items as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">No menu items found.</div>
                    <?php endif; ?>
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