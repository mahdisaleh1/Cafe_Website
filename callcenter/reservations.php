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

// Handle reservation form submission
$reservation_success = false;
$reservation_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reservation'])) {
    $client_name = trim($_POST['client_name']);
    $phone = trim($_POST['phone']);
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $guest_count = intval($_POST['guest_count']);
    $table_id = intval($_POST['table_id']);

    if ($client_name && $phone && $reservation_date && $reservation_time && $guest_count > 0 && $table_id > 0) {
        $stmt = $con->prepare("INSERT INTO reservations (client_name, phone, reservation_date, reservation_time, guest_count, table_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("ssssii", $client_name, $phone, $reservation_date, $reservation_time, $guest_count, $table_id);
        if ($stmt->execute()) {
            $reservation_success = true;
        } else {
            $reservation_error = "Failed to add reservation.";
        }
    } else {
        $reservation_error = "Please fill in all fields.";
    }
}

// Fetch tables for dropdown
$tables = [];
$table_res = $con->query("SELECT id, table_number, seats FROM tables ORDER BY table_number ASC");
while ($t = $table_res->fetch_assoc()) {
    $tables[] = $t;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservations - Barista Café</title>
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
                            <a class="nav-link" href="./tables.php">Tables</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./reservations.php">Reservations</a>
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
                        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                            <i class="bi bi-plus-circle"></i> Add New Reservation
                        </button>
                        <?php if ($reservation_success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Reservation added successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php elseif ($reservation_error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($reservation_error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php
                        // Show update success/error messages
                        if (isset($_SESSION['reservation_update_success'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                                . htmlspecialchars($_SESSION['reservation_update_success']) .
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                            unset($_SESSION['reservation_update_success']);
                        }
                        if (isset($_SESSION['reservation_update_error'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                                . htmlspecialchars($_SESSION['reservation_update_error']) .
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                            unset($_SESSION['reservation_update_error']);
                        }
                        ?>
                    </div>
                </div>
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
                                // Handle update form submission
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reservation'])) {
                                    $update_id = intval($_POST['reservation_id']);
                                    $update_client_name = trim($_POST['client_name']);
                                    $update_phone = trim($_POST['phone']);
                                    $update_date = $_POST['reservation_date'];
                                    $update_time = $_POST['reservation_time'];
                                    $update_guest_count = intval($_POST['guest_count']);
                                    $update_table_id = intval($_POST['table_id']);
                                    $update_status = $_POST['status'];

                                    if ($update_client_name && $update_phone && $update_date && $update_time && $update_guest_count > 0 && $update_table_id > 0 && $update_status) {
                                        $stmt = $con->prepare("UPDATE reservations SET client_name=?, phone=?, reservation_date=?, reservation_time=?, guest_count=?, table_id=?, status=? WHERE id=?");
                                        $stmt->bind_param("ssssissi", $update_client_name, $update_phone, $update_date, $update_time, $update_guest_count, $update_table_id, $update_status, $update_id);
                                        if ($stmt->execute()) {
                                            $_SESSION['reservation_update_success'] = "Reservation updated successfully!";
                                        } else {
                                            $_SESSION['reservation_update_error'] = "Failed to update reservation.";
                                        }
                                    } else {
                                        $_SESSION['reservation_update_error'] = "Please fill in all fields.";
                                    }
                                    // Redirect to avoid resubmission
                                    header("Location: " . $_SERVER['REQUEST_URI']);
                                    exit();
                                }

                                $res = $con->query("SELECT r.*, t.table_number FROM reservations r LEFT JOIN tables t ON r.table_id = t.id ORDER BY r.reservation_date DESC, r.reservation_time DESC");
                                if ($res->num_rows > 0) {
                                    while ($row = $res->fetch_assoc()) {
                                        $row_id = $row['id'];
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['reservation_time']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['guest_count']) . "</td>";
                                        echo "<td>" . (isset($row['table_number']) ? htmlspecialchars($row['table_number']) : '<span class=\"text-muted\">Unassigned</span>') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                        echo "<td>
                                            <button 
                                                class=\"btn btn-primary btn-sm edit-reservation-btn\"
                                                data-bs-toggle=\"modal\"
                                                data-bs-target=\"#editReservationModal\"
                                                data-id=\"" . htmlspecialchars($row['id']) . "\"
                                                data-client_name=\"" . htmlspecialchars($row['client_name'], ENT_QUOTES) . "\"
                                                data-phone=\"" . htmlspecialchars($row['phone'], ENT_QUOTES) . "\"
                                                data-date=\"" . htmlspecialchars($row['reservation_date']) . "\"
                                                data-time=\"" . htmlspecialchars($row['reservation_time']) . "\"
                                                data-guests=\"" . htmlspecialchars($row['guest_count']) . "\"
                                                data-table_id=\"" . htmlspecialchars($row['table_id']) . "\"
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

            <!-- Edit Reservation Modal -->
            <div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content shadow-lg border-0 rounded-4">
                  <div class="modal-header bg-brown text-white rounded-top-4">
                    <h5 class="modal-title" id="editReservationModalLabel">
                      <i class="bi bi-pencil-square me-2"></i>Edit Reservation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body px-4 py-3">
                    <input type="hidden" name="reservation_id" id="edit_reservation_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_client_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control rounded-pill" id="edit_client_name" name="client_name" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control rounded-pill" id="edit_phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_reservation_date" class="form-label">Date</label>
                            <input type="date" class="form-control rounded-pill" id="edit_reservation_date" name="reservation_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_reservation_time" class="form-label">Time</label>
                            <input type="time" class="form-control rounded-pill" id="edit_reservation_time" name="reservation_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_guest_count" class="form-label">Guests</label>
                            <input type="number" class="form-control rounded-pill" id="edit_guest_count" name="guest_count" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_table_id" class="form-label">Assign Table</label>
                            <select class="form-select rounded-pill" id="edit_table_id" name="table_id">
                                <option value="">Select Table</option>
                                <?php foreach ($tables as $table): ?>
                                <option value="<?php echo $table['id']; ?>">
                                    Table <?php echo htmlspecialchars($table['table_number']); ?> (<?php echo htmlspecialchars($table['seats']); ?> seats)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select rounded-pill" id="edit_status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                  </div>
                  <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_reservation" class="btn btn-brown rounded-pill px-4">
                      <i class="bi bi-save me-1"></i>Save Changes
                    </button>
                  </div>
                </form>
              </div>
            </div>
        </section>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editButtons = document.querySelectorAll('.edit-reservation-btn');
            editButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_reservation_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_client_name').value = btn.getAttribute('data-client_name');
                    document.getElementById('edit_phone').value = btn.getAttribute('data-phone');
                    document.getElementById('edit_reservation_date').value = btn.getAttribute('data-date');
                    document.getElementById('edit_reservation_time').value = btn.getAttribute('data-time');
                    document.getElementById('edit_guest_count').value = btn.getAttribute('data-guests');
                    document.getElementById('edit_table_id').value = btn.getAttribute('data-table_id');
                    document.getElementById('edit_status').value = btn.getAttribute('data-status');
                });
            });
        });
        </script>

        <!-- Add Reservation Modal -->
        <div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form method="post" class="modal-content shadow-lg border-0 rounded-4">
              <div class="modal-header bg-brown text-white rounded-top-4">
            <h5 class="modal-title" id="addReservationModalLabel">
              <i class="bi bi-calendar-plus me-2"></i>Add New Reservation
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body px-4 py-3">
              <div class="row g-3">
                  <div class="col-12">
                  <label for="client_name" class="form-label">Customer Name</label>
                  <input type="text" class="form-control rounded-pill" id="client_name" name="client_name" required>
                  </div>
                  <div class="col-12">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" class="form-control rounded-pill" id="phone" name="phone" required>
                  </div>
                  <div class="col-md-6">
                  <label for="reservation_date" class="form-label">Date</label>
                  <input type="date" class="form-control rounded-pill" id="reservation_date" name="reservation_date" required>
                  </div>
                  <div class="col-md-6">
                  <label for="reservation_time" class="form-label">Time</label>
                  <input type="time" class="form-control rounded-pill" id="reservation_time" name="reservation_time" required>
                  </div>
                  <div class="col-md-6">
                  <label for="guest_count" class="form-label">Guests</label>
                  <input type="number" class="form-control rounded-pill" id="guest_count" name="guest_count" min="1" required>
                  </div>
                  <div class="col-md-6">
                  <label for="table_id" class="form-label">Assign Table</label>
                  <select class="form-select rounded-pill" id="table_id" name="table_id" required>
                      <option value="">Select Table</option>
                      <?php foreach ($tables as $table): ?>
                      <option value="<?php echo $table['id']; ?>">
                          Table <?php echo htmlspecialchars($table['table_number']); ?> (<?php echo htmlspecialchars($table['seats']); ?> seats)
                      </option>
                      <?php endforeach; ?>
                  </select>
                  </div>
              </div>
              </div>
              <div class="modal-footer bg-light rounded-bottom-4">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_reservation" class="btn btn-brown rounded-pill px-4">
              <i class="bi bi-plus-circle me-1"></i>Add Reservation
            </button>
              </div>
            </form>
          </div>
        </div>
        <style>
            .bg-brown {
            background: #6c4f3d !important;
            }
            .btn-brown {
            background: #6c4f3d;
            color: #fff;
            border: none;
            }
            .btn-brown:hover, .btn-brown:focus {
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
    <script>
        // If there was a form error, open the modal automatically
        <?php if ($reservation_error): ?>
            var addReservationModal = new bootstrap.Modal(document.getElementById('addReservationModal'));
            addReservationModal.show();
        <?php endif; ?>
    </script>
</body>
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e3c9b6 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
</style>
</html>
