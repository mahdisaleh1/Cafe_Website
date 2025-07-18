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
    <title>Reservations - Admin Barista Café</title>
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
        <section style="margin-top: 140px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div>
                        <h1 class="mb-4">Reservations</h1>
                        <p class="mb-5">View, add, edit, or check details of reservations here.</p>
                        <!-- Add & Search Row Start -->
                        <div class="mb-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                                <form method="get" class="w-100" style="max-width: 100%; width: 100%;">
                                    <div class="row g-2">
                                        <div class="col-12 col-md-5">
                                            <div class="input-group search-bar-menu">
                                                <input type="text" name="search" class="form-control" placeholder="Search reservations..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i><span class="d-none d-md-inline"> Search</span></button>
                                            </div>
                                        </div>
                                        <!-- Date and Filter button: side by side on mobile, stacked on desktop -->
                                        <div class="col-12 col-md-3">
                                            <div class="d-flex flex-row flex-md-row align-items-center">
                                                <label for="reservation_date" class="form-label mb-0 me-2 d-none d-md-block" style="font-size: 0.95rem;">Date</label>
                                                <input type="date" id="reservation_date" name="reservation_date" class="form-control me-2" style="min-width: 120px;">
                                                <button class="btn btn-secondary d-inline d-md-none ms-2" type="submit" style="white-space: nowrap;">
                                                    <i class="bi bi-funnel"></i> Filter
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Filter button for desktop only -->
                                        <div class="col-6 col-md-2 d-none d-md-block">
                                            <button class="btn btn-secondary w-100" type="submit"><i class="bi bi-funnel"></i><span class="d-none d-md-inline"> Filter</span></button>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <?php if (isset($_GET['search']) || isset($_GET['reservation_date'])): ?>
                                                <a href="reservations.php" class="btn btn-outline-secondary w-100">Reset</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                                <div class="w-100 w-md-auto mt-3 mt-md-0 d-flex justify-content-end">
                                    <a href="add_reservation.php" class="btn btn-success w-100 w-md-auto" style="white-space: nowrap;">
                                        <i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Reservation</span>
                                        <span class="d-inline d-md-none">Add</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <style>
                            @media (max-width: 767.98px) {

                                /* Make date and filter button side by side on mobile */
                                #reservation_date {
                                    min-width: 0 !important;
                                    flex: 1 1 auto;
                                }

                                .d-flex.flex-row.flex-md-row.align-items-center {
                                    gap: 8px;
                                }

                                .d-none.d-md-block {
                                    display: none !important;
                                }

                                .d-inline.d-md-none {
                                    display: inline !important;
                                }
                            }
                        </style>
                        <?php
                        // --- Table update for date filter ---
                        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                        $reservation_date = isset($_GET['reservation_date']) ? trim($_GET['reservation_date']) : '';
                        $sql = "SELECT r.*, t.table_number, u.fullname AS user_name 
                                FROM reservations r 
                                LEFT JOIN tables t ON r.table_id = t.id 
                                LEFT JOIN users u ON r.id = u.id";
                        $params = [];
                        $types = '';
                        $where = [];
                        if ($search !== '') {
                            $where[] = "(r.client_name LIKE ? OR r.phone LIKE ? OR t.table_number LIKE ? OR u.fullname LIKE ?)";
                            $like = '%' . $search . '%';
                            $params = [$like, $like, $like, $like];
                            $types = 'ssss';
                        }
                        if ($reservation_date !== '') {
                            $where[] = "r.reservation_date = ?";
                            $params[] = $reservation_date;
                            $types .= 's';
                        }
                        if (count($where) > 0) {
                            $sql .= " WHERE " . implode(' AND ', $where);
                        }
                        $sql .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";
                        if (count($params) > 0) {
                            $stmt = $con->prepare($sql);
                            $stmt->bind_param($types, ...$params);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            $result = $con->query($sql);
                        }
                        $reservationDetails = [];
                        ?>
                        <!-- Add & Search Row End -->
                        <div class="table-responsive">
                            <table class="table table-bordered reservations-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer Name</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Guests</th>
                                        <th>Table</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['reservation_time']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['guest_count']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['table_number']) . "</td>";
                                            echo "<td>" . htmlspecialchars(ucfirst($row['status'])) . "</td>";
                                            echo "<td>";
                                    ?>
                                            <a href="./function/edit_reservation.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm me-2">Edit</a>
                                            <button type="button" class="btn btn-info btn-sm" onclick="showReservationDetail(<?php echo $row['id']; ?>)">Details</button>
                                    <?php
                                            echo "</td>";
                                            echo "</tr>";
                                            // Prepare reservation details for JS
                                            $reservationDetails[$row['id']] = [
                                                'ID' => $row['id'],
                                                'Customer Name' => htmlspecialchars($row['client_name']),
                                                'Phone' => htmlspecialchars($row['phone']),
                                                'Date' => htmlspecialchars($row['reservation_date']),
                                                'Time' => htmlspecialchars($row['reservation_time']),
                                                'Guests' => htmlspecialchars($row['guest_count']),
                                                'Table' => htmlspecialchars($row['table_number']),
                                                'Status' => htmlspecialchars(ucfirst($row['status'])),
                                                'User' => htmlspecialchars($row['user_name']),
                                                'Notes' => htmlspecialchars($row['client_message']),
                                                'Created At' => htmlspecialchars($row['created_at']),
                                            ];
                                        }
                                    } else {
                                        echo "<tr><td colspan='10'>No reservations found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        // Output reservation details as JS object
                        if (!empty($reservationDetails)) {
                            echo "<script>var reservationDetails = " . json_encode($reservationDetails) . ";</script>";
                        } else {
                            echo "<script>var reservationDetails = {};</script>";
                        }
                        ?>
                        <!-- Reservation Details Modal -->
                        <div class="modal fade" id="reservationDetailModal" tabindex="-1" aria-labelledby="reservationDetailModalLabel" aria-hidden="true" style="margin-top: 70px;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reservationDetailModalLabel">Reservation Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="reservationDetailContent">
                                        <!-- Details will be loaded here -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            function showReservationDetail(id) {
                                var details = reservationDetails[id];
                                if (!details) return;
                                var html = '<table class="table table-bordered">';
                                for (var key in details) {
                                    if (details[key] !== null && details[key] !== '') {
                                        html += '<tr><th style="width: 40%;">' + key + '</th><td>' + details[key] + '</td></tr>';
                                    }
                                }
                                html += '</table>';
                                document.getElementById('reservationDetailContent').innerHTML = html;
                                var modal = new bootstrap.Modal(document.getElementById('reservationDetailModal'));
                                modal.show();
                            }
                        </script>
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

    .reservations-table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        margin-top: 30px;
    }

    .reservations-table th,
    .reservations-table td {
        vertical-align: middle;
        text-align: center;
    }

    .reservations-table th {
        background: var(--custom-btn-bg-color, #6c4f3d);
        color: #fff;
        font-weight: 600;
        border: none;
    }

    .reservations-table td {
        border-top: 1px solid #eee;
        font-size: 1rem;
    }

    .reservations-table tr:hover {
        background: #f8f9fa;
    }

    .reservations-table .btn {
        min-width: 70px;
    }

    .reservations-table .btn-primary {
        background: var(--custom-btn-bg-color, #6c4f3d);
        border: none;
    }

    .reservations-table .btn-primary:hover {
        background: #563927;
    }

    .reservations-table .btn-info {
        background: #3498db;
        border: none;
        color: #fff;
    }

    .reservations-table .btn-info:hover {
        background: #217dbb;
    }
</style>

</html>