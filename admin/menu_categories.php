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
    <title>Menu Categories - Admin Barista Café</title>
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
                            <a class="nav-link click-scroll active" href="./menu_categories.php">Menu Categories</a>
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
                    <div class="col-lg-8 col-12 text-center">
                        <h1 class="mb-4">Menu Categories</h1>
                        <p class="mb-5">Manage your menu categories here. You can add, edit, or delete categories.</p>

                        <?php
                        // Handle form submission
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
                            $category_name = trim($_POST['category_name']);
                            if (!empty($category_name)) {
                                $stmt = $con->prepare("INSERT INTO menu_categories (name) VALUES (?)");
                                $stmt->bind_param("s", $category_name);
                                if ($stmt->execute()) {
                                    echo '<div class="alert alert-success">Category added successfully.</div>';
                                } else {
                                    echo '<div class="alert alert-danger">Error adding category.</div>';
                                }
                                $stmt->close();
                            } else {
                                echo '<div class="alert alert-warning">Category name cannot be empty.</div>';
                            }
                        }
                        ?>

                        <form method="post" class="mb-4" style="max-width:400px;margin:0 auto;">
                            <div class="input-group">
                                <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
                                <button type="submit" class="btn btn-success">Add Category</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered menu-categories-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM menu_categories";
                                    $result = $con->query($query);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                            echo "<td>";
                                    ?>

                                            <!-- Edit Button triggers modal -->
                                            <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $row['id']; ?>">
                                                Edit
                                            </button>

                                            <!-- Edit Category Modal -->
                                            <div class="modal fade" id="editCategoryModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editCategoryModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post" action="./function/edit_category.php">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editCategoryModalLabel<?php echo $row['id']; ?>">Edit Category</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="category_id" value="<?php echo $row['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label for="category_name_<?php echo $row['id']; ?>" class="form-label">Category Name</label>
                                                                    <input type="text" class="form-control" id="category_name_<?php echo $row['id']; ?>" name="category_name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <a href='./function/delete_category.php?id=<?php echo $row['id']; ?>' class='btn btn-danger btn-sm' onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                    <?php
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No categories found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </section>
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
        /* center horizontally and add vertical space */
        display: block;
        /* required for auto margins to take effect */
        text-align: center;
    }

    .navbar-nav .nav-link.notactive {
        color: #fff !important;
    }
    
</style>

</html>