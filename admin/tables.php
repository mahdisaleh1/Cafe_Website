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
    <title>Users - Admin Barista Café</title>
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
                            <a class="nav-link click-scroll notactive" href="./admin_dashboard.php">Home</a>
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
                            <a class="nav-link click-scroll active" href="./tables.php">Tables</a>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Tables Management</h2>
                    <a href="./table_history.php" class="btn btn-success"> 
                        <i class="bi bi-plus-square"> </i> Check Tables History
                    </a>
                </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Table Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all tables
                        $tables_query = "SELECT * FROM tables";
                        $tables_result = $con->query($tables_query);
                        $i = 1;
                        while ($table = $tables_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                                <td>
                                    <?php
                                        if ($table['status'] === 'occupied') {
                                            echo '<span class="badge bg-danger">Occupied</span>';
                                        } elseif ($table['status'] === 'available') {
                                            echo '<span class="badge bg-success">Available</span>';
                                        } elseif ($table['status'] === 'closed') {
                                            echo '<span class="badge bg-secondary">Closed</span>';
                                        } else {
                                            echo '<span class="badge bg-light text-dark">' . htmlspecialchars($table['status']) . '</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <!-- Edit Status Button -->
                                    <button
                                        class="btn btn-primary btn-sm editTableStatusBtn"
                                        data-id="<?php echo $table['id']; ?>"
                                        data-status="<?php echo htmlspecialchars($table['status']); ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTableStatusModal">
                                        <i class="bi bi-pencil-square"></i> Edit Status
                                    </button>
                                    <!-- View Orders Button -->
                                    <button
                                        class="btn btn-info btn-sm viewTableOrdersBtn"
                                        data-id="<?php echo $table['id']; ?>"
                                        data-number="<?php echo htmlspecialchars($table['table_number']); ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewTableOrdersModal">
                                        <i class="bi bi-list-ul"></i> View Orders
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Edit Table Status Modal -->
            <div class="modal fade" id="editTableStatusModal" tabindex="-1" aria-labelledby="editTableStatusModalLabel" aria-hidden="true" style="margin-top: 100px;">
                <div class="modal-dialog">
                    <form method="post" action="" class="modal-content" id="editTableStatusForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTableStatusModalLabel">Edit Table Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editTableId" name="table_id">
                            <div class="mb-3">
                                <label for="editTableStatus" class="form-label">Status</label>
                                <select class="form-select" id="editTableStatus" name="table_status" required>
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <input type="hidden" name="edit_table_status_submit" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View Table Orders Modal -->
            <div class="modal fade" id="viewTableOrdersModal" tabindex="-1" aria-labelledby="viewTableOrdersModalLabel" aria-hidden="true" style="margin-top: 100px;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewTableOrdersModalLabel">Orders for Table <span id="ordersTableNumber"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="tableOrdersContent">
                            <!-- Orders will be loaded here via AJAX -->
                            <div class="text-center text-muted">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Handle Edit Table Status POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_table_status_submit'])) {
                $table_id = intval($_POST['table_id']);
                $table_status = trim($_POST['table_status']);
                $update_query = "UPDATE tables SET status=? WHERE id=?";
                $stmt = $con->prepare($update_query);
                $stmt->bind_param("si", $table_status, $table_id);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Table status updated successfully.</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to update table status.</div>';
                }
            }
            ?>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Edit Table Status
                    document.querySelectorAll('.editTableStatusBtn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            document.getElementById('editTableId').value = btn.getAttribute('data-id');
                            document.getElementById('editTableStatus').value = btn.getAttribute('data-status');
                        });
                    });

                    // View Table Orders
                    document.querySelectorAll('.viewTableOrdersBtn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var tableId = btn.getAttribute('data-id');
                            var tableNumber = btn.getAttribute('data-number');
                            document.getElementById('ordersTableNumber').textContent = tableNumber;
                            var content = document.getElementById('tableOrdersContent');
                            content.innerHTML = '<div class="text-center text-muted">Loading...</div>';
                            // AJAX to fetch orders for this table
                            fetch('./function/fetch_table_orders.php?table_id=' + tableId)
                                .then(response => response.text())
                                .then(html => {
                                    content.innerHTML = html;
                                })
                                .catch(() => {
                                    content.innerHTML = '<div class="alert alert-danger">Failed to load orders.</div>';
                                });
                        });
                    });
                });
            </script>
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
    .navbar-nav .nav-link.notactive {
        color: #fff !important;
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