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
                            <a class="nav-link click-scroll" href="./tables.php">Tables</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll active" href="./users.php">Users</a>
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
                    <h2 class="mb-0">Users Management</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Add New User
                    </button>
                </div>
                <!-- Search Bar -->
                <form class="mb-3" method="get" id="userSearchForm">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="userSearchInput" placeholder="Search users by name, email, role, or status..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
                    </div>
                </form>
                <?php
                // Handle edit user POST (AJAX or normal POST)
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_submit'])) {
                    $edit_id = intval($_POST['id']);
                    $edit_fullname = trim($_POST['fullname']);
                    $edit_email = trim($_POST['email']);
                    $edit_role = trim($_POST['role']);
                    $edit_status = trim($_POST['status']);
                    $edit_password = $_POST['password'];

                    // Update query
                    if ($edit_password !== '') {
                        $hashed_password = password_hash($edit_password, PASSWORD_DEFAULT);
                        $update_query = "UPDATE users SET fullname=?, email=?, role=?, status=?, password=? WHERE id=?";
                        $stmt = $con->prepare($update_query);
                        $stmt->bind_param("sssssi", $edit_fullname, $edit_email, $edit_role, $edit_status, $hashed_password, $edit_id);
                    } else {
                        $update_query = "UPDATE users SET fullname=?, email=?, role=?, status=? WHERE id=?";
                        $stmt = $con->prepare($update_query);
                        $stmt->bind_param("ssssi", $edit_fullname, $edit_email, $edit_role, $edit_status, $edit_id);
                    }
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success">User updated successfully.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Failed to update user.</div>';
                    }
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle bg-white">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                            if ($search !== '') {
                                $search_param = "%{$search}%";
                                $users_query = "SELECT * FROM users WHERE fullname LIKE ? OR email LIKE ? OR role LIKE ? OR status LIKE ?";
                                $stmt = $con->prepare($users_query);
                                $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
                                $stmt->execute();
                                $users_result = $stmt->get_result();
                            } else {
                                $users_query = "SELECT * FROM users";
                                $users_result = $con->query($users_query);
                            }
                            $i = 1;
                            while ($user = $users_result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td>
                                        <?php if (isset($user['status']) && $user['status'] === 'inactive'): ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-info btn-sm viewUserBtn"
                                            data-id="<?php echo $user['id']; ?>"
                                            data-fullname="<?php echo htmlspecialchars($user['fullname']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-role="<?php echo htmlspecialchars($user['role']); ?>"
                                            data-date="<?php echo htmlspecialchars($user['date_of_creation']); ?>"
                                            data-status="<?php echo isset($user['status']) ? htmlspecialchars($user['status']) : 'active'; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewUserModal">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                        <button
                                            class="btn btn-primary btn-sm editUserBtn"
                                            data-id="<?php echo $user['id']; ?>"
                                            data-fullname="<?php echo htmlspecialchars($user['fullname']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-role="<?php echo htmlspecialchars($user['role']); ?>"
                                            data-status="<?php echo isset($user['status']) ? htmlspecialchars($user['status']) : 'active'; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true" style="margin-top: 100px;">
                <div class="modal-dialog">
                    <form method="post" action="./function/add_user.php" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="addFullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="addFullname" name="add_fullname" required>
                            </div>
                            <div class="mb-3">
                                <label for="addEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="addEmail" name="add_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="addPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="addPassword" name="add_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="addRole" class="form-label">Role</label>
                                <select class="form-select" id="addRole" name="add_role" required>
                                    <option value="admin">Admin</option>
                                    <option value="waiter">Waiter</option>
                                    <option value="callcenter">Call Center</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="addStatus" class="form-label">Status</label>
                                <select class="form-select" id="addStatus" name="add_status" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Add User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" style="margin-top: 100px;">
                <div class="modal-dialog">
                    <form method="post" action="" class="modal-content" id="editUserForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editUserId" name="id">
                            <div class="mb-3">
                                <label for="editFullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="editFullname" name="fullname" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Role</label>
                                <select class="form-select" id="editRole" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="waiter">Waiter</option>
                                    <option value="callcenter">Call Center</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editStatus" class="form-label">Status</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" id="editPassword" name="password">
                            </div>
                            <input type="hidden" name="edit_user_submit" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View User Modal -->
            <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true" style="margin-top: 120px;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <dl class="row">
                                <dt class="col-sm-4">Full Name</dt>
                                <dd class="col-sm-8" id="viewFullname"></dd>

                                <dt class="col-sm-4">Email</dt>
                                <dd class="col-sm-8" id="viewEmail"></dd>

                                <dt class="col-sm-4">Role</dt>
                                <dd class="col-sm-8" id="viewRole"></dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8" id="viewStatus"></dd>

                                <dt class="col-sm-4">Date of Creation</dt>
                                <dd class="col-sm-8" id="viewDate"></dd>
                            </dl>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Edit User
                var editUserBtns = document.querySelectorAll('.editUserBtn');
                editUserBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.getElementById('editUserId').value = btn.getAttribute('data-id');
                        document.getElementById('editFullname').value = btn.getAttribute('data-fullname');
                        document.getElementById('editEmail').value = btn.getAttribute('data-email');
                        document.getElementById('editRole').value = btn.getAttribute('data-role');
                        document.getElementById('editStatus').value = btn.getAttribute('data-status');
                        document.getElementById('editPassword').value = '';
                    });
                });

                // View User
                var viewUserBtns = document.querySelectorAll('.viewUserBtn');
                viewUserBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.getElementById('viewFullname').textContent = btn.getAttribute('data-fullname');
                        document.getElementById('viewEmail').textContent = btn.getAttribute('data-email');
                        document.getElementById('viewRole').textContent = btn.getAttribute('data-role');
                        document.getElementById('viewDate').textContent = btn.getAttribute('data-date');
                        var status = btn.getAttribute('data-status');
                        document.getElementById('viewStatus').innerHTML = status === 'inactive' ?
                            '<span class="badge bg-danger">Inactive</span>' :
                            '<span class="badge bg-success">Active</span>';
                    });
                });
            });
        </script>


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