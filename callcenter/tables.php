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

// Fetch table statuses
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$status_sql = "";
$params = [];
$types = "";

if ($status_filter && in_array($status_filter, ['available', 'closed', 'occupied'])) {
    $status_sql = " WHERE status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$table_query = "SELECT * FROM tables" . $status_sql . " ORDER BY id ASC";
$stmt_tables = $con->prepare($table_query);
if ($params) {
    $stmt_tables->bind_param($types, ...$params);
}
$stmt_tables->execute();
$tables_result = $stmt_tables->get_result();
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
                            <a class="nav-link" href="./callcenter_dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./menu.php">Menu Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./tables.php">Tables</a>
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
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8">
                        <h1 class="text-center text-coffee mb-3"><i class="bi bi-table me-2"></i>Tables Availability</h1>
                        <p class="text-center text-muted">Manage and view the status of tables in the café.</p>
                    </div>
                </div>
                <!-- Redesigned Table Availability Section -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-10">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-coffee text-white d-flex align-items-center justify-content-between" style="background: #6c4f3d;">
                                <h4 class="mb-0"><i class="bi bi-table me-2"></i>Tables Overview</h4>
                                <form method="get" class="d-flex align-items-center gap-2">
                                    <label for="status" class="form-label mb-0 me-2 text-white">Status:</label>
                                    <select name="status" id="status" class="form-select form-select-sm w-auto">
                                        <option value="">All</option>
                                        <option value="available" <?php if($status_filter=='available') echo 'selected'; ?>>Available</option>
                                        <option value="reserved" <?php if($status_filter=='closed') echo 'selected'; ?>>Closed</option>
                                        <option value="occupied" <?php if($status_filter=='occupied') echo 'selected'; ?>>Occupied</option>
                                    </select>
                                    <button type="submit" class="btn btn-light btn-sm ms-2">Filter</button>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-dark" style="background: #a67c52;">
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 35%;">Table Number</th>
                                                <th style="width: 20%;">Seats</th>
                                                <th style="width: 20%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($tables_result->num_rows > 0): ?>
                                                <?php while($table = $tables_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td class="fw-bold"><?php echo htmlspecialchars($table['id']); ?></td>
                                                        <td>
                                                            <i class="bi bi-cup-hot me-2 text-coffee"></i>
                                                            <?php echo htmlspecialchars($table['table_number']); ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary px-3 py-2">
                                                                <i class="bi bi-people me-1"></i>
                                                                <?php echo htmlspecialchars($table['seats']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                $status = $table['status'];
                                                                $badge = 'secondary';
                                                                $icon = 'bi-question-circle';
                                                                if ($status == 'available') {
                                                                    $badge = 'success';
                                                                    $icon = 'bi-check-circle';
                                                                } elseif ($status == 'closed') {
                                                                    $badge = 'warning';
                                                                    $icon = 'bi-clock-history';
                                                                } elseif ($status == 'occupied') {
                                                                    $badge = 'danger';
                                                                    $icon = 'bi-person-fill-lock';
                                                                }
                                                            ?>
                                                            <span class="badge bg-<?php echo $badge; ?> d-inline-flex align-items-center px-3 py-2">
                                                                <i class="bi <?php echo $icon; ?> me-1"></i>
                                                                <?php echo ucfirst($status); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">
                                                        <i class="bi bi-exclamation-circle me-2"></i>No tables found for this status.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-end bg-light py-2 small text-muted">
                                Showing <?php echo $tables_result->num_rows; ?> table<?php echo $tables_result->num_rows == 1 ? '' : 's'; ?>.
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Redesigned Table Availability Section -->
            </div>
        </section>
        <style>
            .bg-coffee {
                background: #6c4f3d !important;
            }
            .text-coffee {
                color: #a67c52 !important;
            }
            .table thead th {
                vertical-align: middle;
            }
            .badge.bg-secondary {
                background: #bfa084 !important;
                color: #fff;
            }
            .badge.bg-success {
                background: #4caf50 !important;
            }
            .badge.bg-warning {
                background: #ffc107 !important;
                color: #6c4f3d;
            }
            .badge.bg-danger {
                background: #dc3545 !important;
            }
        </style>

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
