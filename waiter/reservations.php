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
// Fetch tables for dropdown
$tables = [];
$table_res = $con->query("SELECT id, table_number, seats FROM tables ORDER BY table_number ASC");
while ($t = $table_res->fetch_assoc()) {
    $tables[] = $t;
}

// Handle search/filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : '';
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(r.client_name LIKE ? OR r.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}
if ($filter_date !== '') {
    $where[] = "r.reservation_date = ?";
    $params[] = $filter_date;
    $types .= 's';
}
$where_sql = '';
if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
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
                <div class="row mb-4">
                    <div class="col-lg-12 text-center">
                        <h1>Reservations</h1>
                        <p>View all reservations and manage them below.</p>
                        <?php
                        if (isset($_SESSION['reservation_update_success'])) {
                            echo '<div class="alert alert-success">'.$_SESSION['reservation_update_success'].'</div>';
                            unset($_SESSION['reservation_update_success']);
                        }
                        if (isset($_SESSION['reservation_update_error'])) {
                            echo '<div class="alert alert-danger">'.$_SESSION['reservation_update_error'].'</div>';
                            unset($_SESSION['reservation_update_error']);
                        }
                        ?>
                    </div>
                </div>
                <!-- Search/Filter Bar -->
                <div class="row mb-3">
                    <div class="col-md-8 mx-auto">
                        <form class="row g-2 align-items-center justify-content-center" method="get" action="">
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="search" placeholder="Search by name or phone" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                            </div>
                            <div class="col-sm-3 d-flex gap-2">
                                <button type="submit" class="btn btn-brown w-100"><i class="bi bi-search"></i> Search</button>
                                <a href="reservations.php" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Search/Filter Bar -->
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer Name</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Guests</th>
                                        <th>Table</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Handle update form submission (status only)
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reservation'])) {
                                        $update_id = intval($_POST['reservation_id']);
                                        $update_status = $_POST['status'];

                                        if ($update_id > 0 && $update_status) {
                                            $stmt = $con->prepare("UPDATE reservations SET status=? WHERE id=?");
                                            $stmt->bind_param("si", $update_status, $update_id);
                                            if ($stmt->execute()) {
                                                $_SESSION['reservation_update_success'] = "Reservation status updated successfully!";
                                            } else {
                                                $_SESSION['reservation_update_error'] = "Failed to update reservation status.";
                                            }
                                        } else {
                                            $_SESSION['reservation_update_error'] = "Please select a status.";
                                        }
                                        // Redirect to avoid resubmission
                                        header("Location: " . $_SERVER['REQUEST_URI']);
                                        exit();
                                    }

                                    // Prepare and execute filtered query
                                    $sql = "SELECT r.*, t.table_number FROM reservations r LEFT JOIN tables t ON r.table_id = t.id $where_sql ORDER BY r.reservation_date DESC, r.reservation_time DESC";
                                    if (count($params) > 0) {
                                        $stmt = $con->prepare($sql);
                                        $stmt->bind_param($types, ...$params);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                    } else {
                                        $res = $con->query($sql);
                                    }

                                    if ($res && $res->num_rows > 0) {
                                        while ($row = $res->fetch_assoc()) {
                                            $row_id = $row['id'];
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['reservation_time']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['guest_count']) . "</td>";
                                            echo "<td>" . (isset($row['table_number']) ? htmlspecialchars($row['table_number']) : '<span class="text-muted">Unassigned</span>') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                            echo "<td>
                                            <button 
                                                class=\"btn btn-primary btn-sm edit-reservation-btn\"
                                                data-bs-toggle=\"modal\"
                                                data-bs-target=\"#editReservationModal\"
                                                data-id=\"" . htmlspecialchars($row['id']) . "\"
                                                data-status=\"" . htmlspecialchars($row['status']) . "\"
                                            >
                                                <i class=\"bi bi-pencil-square\"></i> Edit
                                            </button>
                                        </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo '<tr><td colspan="10" class="text-center text-muted">No reservations found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Edit Reservation Modal (Status Only) -->
        <div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true" style="margin-top: 100px;">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <div class="modal-header bg-brown text-white rounded-top-4">
                        <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation Status</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="reservation_id" id="edit_reservation_id">
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_reservation" class="btn btn-brown">Update Status</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var editButtons = document.querySelectorAll('.edit-reservation-btn');
                editButtons.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.getElementById('edit_reservation_id').value = btn.getAttribute('data-id');
                        document.getElementById('edit_status').value = btn.getAttribute('data-status');
                    });
                });
            });
        </script>
        <style>
            .bg-brown {
                background: #6c4f3d !important;
            }

            .btn-brown {
                background: #6c4f3d;
                color: #fff;
                border: none;
            }

            .btn-brown:hover,
            .btn-brown:focus {
                background: #543c2d;
                color: #fff;
            }

            .modal-content {
                border-radius: 1.5rem;
            }

            .rounded-top-4 {
                border-top-left-radius: 1.5rem !important;
                border-top-right-radius: 1.5rem !important;
            }

            .rounded-bottom-4 {
                border-bottom-left-radius: 1.5rem !important;
                border-bottom-right-radius: 1.5rem !important;
            }
        </style>

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