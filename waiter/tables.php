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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['table_id'], $_POST['new_status'])) {
    $table_id = intval($_POST['table_id']);
    $new_status = $_POST['new_status'];
    $allowed_status = ['occupied', 'reserved'];
    if (in_array($new_status, $allowed_status)) {
        $stmt = $con->prepare("UPDATE tables SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $table_id);
        $stmt->execute();

        // If status is set to 'occupied', create a new session for this table
        if ($new_status === 'occupied') {
            // Check if there is already an open session for this table
            $check = $con->prepare("SELECT id FROM table_sessions WHERE table_id = ? AND ended_at IS NULL");
            $check->bind_param("i", $table_id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows === 0) {
                $now = date('Y-m-d H:i:s');
                $insert = $con->prepare("INSERT INTO table_sessions (table_id, started_at, created_by) VALUES (?, ?, ?)");
                $insert->bind_param("isi", $table_id, $now, $user_id);
                $insert->execute();
            }
        }
    }
    header("Location: tables.php");
    exit();
}

// Handle mark as paid (set status to closed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'], $_POST['table_id'])) {
    $table_id = intval($_POST['table_id']);
    // Update table status to 'closed'
    $stmt = $con->prepare("UPDATE tables SET status = 'closed' WHERE id = ?");
    $stmt->bind_param("i", $table_id);
    $stmt->execute();

    // Close the open session for this table (set ended_at)
    $now = date('Y-m-d H:i:s');
    $updateSession = $con->prepare("UPDATE table_sessions SET ended_at = ? WHERE table_id = ? AND ended_at IS NULL");
    $updateSession->bind_param("si", $now, $table_id);
    $updateSession->execute();

    // Get the session_id of the session just closed
    $sessionQuery = $con->prepare("SELECT id FROM table_sessions WHERE table_id = ? AND ended_at = ?");
    $sessionQuery->bind_param("is", $table_id, $now);
    $sessionQuery->execute();
    $sessionResult = $sessionQuery->get_result();
    if ($sessionRow = $sessionResult->fetch_assoc()) {
        $session_id = $sessionRow['id'];
        // Update all orders for this session to status 'paid'
        $updateOrders = $con->prepare("UPDATE table_orders SET status = 'paid' WHERE session_id = ?");
        $updateOrders->bind_param("i", $session_id);
        $updateOrders->execute();
    }

    header("Location: tables.php");
    exit();
}

// Fetch tables
$tables = [];
$result = $con->query("SELECT * FROM tables ORDER BY table_number ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tables & Orders - Barista Café</title>
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
                            <a class="nav-link" href="./waiter_dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./menu.php">Menu Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./tables.php">Tables & Orders</a>
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
                        <h1>Tables</h1>
                        <p>Manage tables, update status, and create orders.</p>
                    </div>
                    <div class="col-lg-10 mx-auto mb-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Table Number</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($tables) > 0): ?>
                                        <?php foreach ($tables as $index => $table): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                                                <td>
                                                    <?php
                                                    $status = $table['status'];
                                                    $badge = 'secondary';
                                                    if ($status == 'available') $badge = 'success';
                                                    elseif ($status == 'occupied') $badge = 'warning';
                                                    elseif ($status == 'reserved') $badge = 'info';
                                                    elseif ($status == 'closed') $badge = 'dark';
                                                    ?>
                                                    <span class="badge bg-<?php echo $badge; ?>">
                                                        <?php echo ucfirst($status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <!-- Update Status Form -->
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="table_id" value="<?php echo $table['id']; ?>">
                                                        <select name="new_status" class="form-select d-inline w-auto" style="display:inline-block;">
                                                            <option value="available" <?php if ($status == 'available') echo 'selected'; ?>>Available</option>
                                                            <option value="occupied" <?php if ($status == 'occupied') echo 'selected'; ?>>Occupied</option>
                                                            <option value="reserved" <?php if ($status == 'reserved') echo 'selected'; ?>>Reserved</option>
                                                            <option value="closed" <?php if ($status == 'closed') echo 'selected'; ?>>Closed</option>
                                                        </select>
                                                        <button type="submit" name="update_status" class="btn btn-sm btn-outline-primary">Update</button>
                                                    </form>
                                                    <!-- Create Order Button -->
                                                    <?php if ($status == 'occupied'): ?>
                                                        <?php
                                                        // Fetch menu categories and items for the modal
                                                        $categories = [];
                                                        $itemsByCategory = [];
                                                        $catResult = $con->query("SELECT * FROM menu_categories ORDER BY name ASC");
                                                        if ($catResult) {
                                                            while ($cat = $catResult->fetch_assoc()) {
                                                                $categories[] = $cat;
                                                                $itemsByCategory[$cat['id']] = [];
                                                            }
                                                        }
                                                        $itemResult = $con->query("SELECT * FROM menu_items ORDER BY name ASC");
                                                        if ($itemResult) {
                                                            while ($item = $itemResult->fetch_assoc()) {
                                                                if (isset($itemsByCategory[$item['category_id']])) {
                                                                    $itemsByCategory[$item['category_id']][] = $item;
                                                                }
                                                            }
                                                        }
                                                        ?>

                                                        <button type="button" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#createOrderModal<?php echo $table['id']; ?>">
                                                            <i class="bi bi-plus-circle"></i> Create Order
                                                        </button>

                                                        <!-- Create Order Modal -->
                                                        <div class="modal fade" id="createOrderModal<?php echo $table['id']; ?>" tabindex="-1" aria-labelledby="createOrderModalLabel<?php echo $table['id']; ?>" aria-hidden="true" style="margin-top: 100px;">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <form action="./function/create_order.php" method="post" id="orderForm<?php echo $table['id']; ?>">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="createOrderModalLabel<?php echo $table['id']; ?>">Create Order for Table <?php echo htmlspecialchars($table['table_number']); ?></h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="table_id" value="<?php echo $table['id']; ?>">
                                                                            <div class="mb-3">
                                                                                <label for="order_notes_<?php echo $table['id']; ?>" class="form-label">Order Notes</label>
                                                                                <textarea class="form-control" id="order_notes_<?php echo $table['id']; ?>" name="order_notes" rows="2" placeholder="Any notes..."></textarea>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Add Items</label>
                                                                                <div id="order-items-list-<?php echo $table['id']; ?>">
                                                                                    <!-- JS will add item rows here -->
                                                                                </div>
                                                                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addOrderItemRow<?php echo $table['id']; ?>()">+ Add Item</button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit" class="btn btn-success">Create Order</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            // Data for categories and items
                                                            const categories<?php echo $table['id']; ?> = <?php echo json_encode($categories); ?>;
                                                            const itemsByCategory<?php echo $table['id']; ?> = <?php echo json_encode($itemsByCategory); ?>;
                                                            let orderItemIndex<?php echo $table['id']; ?> = 0;

                                                            function addOrderItemRow<?php echo $table['id']; ?>() {
                                                                const idx = orderItemIndex<?php echo $table['id']; ?>++;
                                                                const container = document.getElementById('order-items-list-<?php echo $table['id']; ?>');
                                                                const row = document.createElement('div');
                                                                row.className = 'row g-2 align-items-end mb-2 order-item-row';
                                                                row.setAttribute('data-idx', idx);

                                                                // Category select
                                                                let catSelect = '<div class="col-md-4"><label class="form-label mb-0">Category</label><select class="form-select form-select-sm order-cat" name="items[' + idx + '][category_id]" onchange="updateItemsSelect<?php echo $table['id']; ?>(this, ' + idx + ')" required><option value="">Choose...</option>';
                                                                categories<?php echo $table['id']; ?>.forEach(cat => {
                                                                    catSelect += '<option value="' + cat.id + '">' + cat.name + '</option>';
                                                                });
                                                                catSelect += '</select></div>';

                                                                // Item select (empty initially)
                                                                let itemSelect = '<div class="col-md-4"><label class="form-label mb-0">Item</label><select class="form-select form-select-sm order-item" name="items[' + idx + '][item_id]" required><option value="">Select category first</option></select></div>';

                                                                // Quantity input
                                                                let qtyInput = '<div class="col-md-3"><label class="form-label mb-0">Qty</label><input type="number" min="1" value="1" class="form-control form-control-sm" name="items[' + idx + '][quantity]" required></div>';

                                                                // Remove button
                                                                let removeBtn = '<div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="removeOrderItemRow<?php echo $table['id']; ?>(this)">&times;</button></div>';

                                                                row.innerHTML = catSelect + itemSelect + qtyInput + removeBtn;
                                                                container.appendChild(row);
                                                            }

                                                            function updateItemsSelect<?php echo $table['id']; ?>(catSelect, idx) {
                                                                const catId = catSelect.value;
                                                                const row = catSelect.closest('.order-item-row');
                                                                const itemSelect = row.querySelector('.order-item');
                                                                let options = '<option value="">Choose item...</option>';
                                                                if (itemsByCategory<?php echo $table['id']; ?>[catId]) {
                                                                    itemsByCategory<?php echo $table['id']; ?>[catId].forEach(item => {
                                                                        options += '<option value="' + item.id + '">' + item.name + '</option>';
                                                                    });
                                                                }
                                                                itemSelect.innerHTML = options;
                                                            }

                                                            function removeOrderItemRow<?php echo $table['id']; ?>(btn) {
                                                                btn.closest('.order-item-row').remove();
                                                            }

                                                            // Add one row by default when modal opens
                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                $('#createOrderModal<?php echo $table['id']; ?>').on('shown.bs.modal', function() {
                                                                    const container = document.getElementById('order-items-list-<?php echo $table['id']; ?>');
                                                                    if (container.childElementCount === 0) {
                                                                        addOrderItemRow<?php echo $table['id']; ?>();
                                                                    }
                                                                });
                                                                // Optional: clear rows when modal closes
                                                                $('#createOrderModal<?php echo $table['id']; ?>').on('hidden.bs.modal', function() {
                                                                    const container = document.getElementById('order-items-list-<?php echo $table['id']; ?>');
                                                                    container.innerHTML = '';
                                                                    orderItemIndex<?php echo $table['id']; ?> = 0;
                                                                });
                                                            });
                                                        </script>
                                                        <!-- Check Orders Button -->
                                                        <!-- Check Orders Button (opens modal) -->
                                                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#ordersModal<?php echo $table['id']; ?>">
                                                            <i class="bi bi-list-ul"></i> Check Orders
                                                        </button>

                                                        <!-- Orders Modal -->
                                                        <div class="modal fade" id="ordersModal<?php echo $table['id']; ?>" tabindex="-1" aria-labelledby="ordersModalLabel<?php echo $table['id']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="ordersModalLabel<?php echo $table['id']; ?>">Orders for Table <?php echo htmlspecialchars($table['table_number']); ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <?php
                                                                        // Fetch the latest open session for this table
                                                                        $sessionStmt = $con->prepare("SELECT id FROM table_sessions WHERE table_id = ? AND ended_at IS NULL LIMIT 1");
                                                                        $sessionStmt->bind_param("i", $table['id']);
                                                                        $sessionStmt->execute();
                                                                        $sessionResult = $sessionStmt->get_result();
                                                                        $sessionId = null;
                                                                        if ($sessionRow = $sessionResult->fetch_assoc()) {
                                                                            $sessionId = $sessionRow['id'];
                                                                        }

                                                                        if ($sessionId) {
                                                                            // Fetch orders for this session
                                                                            $ordersStmt = $con->prepare("SELECT * FROM table_orders WHERE session_id = ? ORDER BY created_at DESC");
                                                                            $ordersStmt->bind_param("i", $sessionId);
                                                                            $ordersStmt->execute();
                                                                            $ordersResult = $ordersStmt->get_result();
                                                                            if ($ordersResult->num_rows > 0) {
                                                                                while ($order = $ordersResult->fetch_assoc()) {
                                                                                    echo '<div class="mb-4 border rounded p-3">';
                                                                                    echo '<div class="mb-2"><strong>Order #'.$order['id'].'</strong> <span class="badge bg-secondary">'.htmlspecialchars(ucfirst($order['status'])).'</span> <span class="text-muted small">'.htmlspecialchars($order['created_at']).'</span></div>';
                                                                                    if (!empty($order['notes'])) {
                                                                                        echo '<div class="mb-2"><em>Notes: '.htmlspecialchars($order['notes']).'</em></div>';
                                                                                    }
                                                                                    // Fetch order items
                                                                                    $itemsStmt = $con->prepare("SELECT oi.*, mi.name AS item_name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
                                                                                    $itemsStmt->bind_param("i", $order['id']);
                                                                                    $itemsStmt->execute();
                                                                                    $itemsResult = $itemsStmt->get_result();
                                                                                    if ($itemsResult->num_rows > 0) {
                                                                                        echo '<table class="table table-sm table-bordered mb-0">';
                                                                                        echo '<thead><tr><th>Item</th><th>Qty</th></tr></thead><tbody>';
                                                                                        while ($item = $itemsResult->fetch_assoc()) {
                                                                                            echo '<tr>';
                                                                                            echo '<td>'.htmlspecialchars($item['item_name']).'</td>';
                                                                                            echo '<td>'.intval($item['quantity']).'</td>';
                                                                                            echo '</tr>';
                                                                                        }
                                                                                        echo '</tbody></table>';
                                                                                    } else {
                                                                                        echo '<div class="text-muted">No items in this order.</div>';
                                                                                    }
                                                                                    echo '</div>';
                                                                                }
                                                                            } else {
                                                                                echo '<div class="text-center text-muted">No orders found for this table.</div>';
                                                                            }
                                                                        } else {
                                                                            echo '<div class="text-center text-muted">No session found for this table.</div>';
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <!-- Mark as Paid Button -->
                                                    <?php if ($status == 'occupied'): ?>
                                                        <form method="post" class="d-inline ms-2">
                                                            <input type="hidden" name="table_id" value="<?php echo $table['id']; ?>">
                                                            <button type="submit" name="mark_paid" class="btn btn-sm btn-dark">
                                                                <i class="bi bi-cash"></i> Mark as Paid
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No tables found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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