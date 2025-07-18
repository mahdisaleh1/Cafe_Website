<?php
// /c:/xampp/htdocs/baristacafe/waiter/function/create_order.php
require_once '../../config.php'; // Adjust path as needed
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


// Get POST data from form
$table_id = isset($_POST['table_id']) ? intval($_POST['table_id']) : 0;
$items = isset($_POST['items']) ? $_POST['items'] : []; // Expecting array: [['item_id'=>1, 'quantity'=>2], ...]

// Validate input
if ($table_id <= 0 || empty($items)) {
    die('Invalid table or items.');
}

if ($con->connect_error) {
    die('Database connection failed: ' . $con->connect_error);
}

// 1. Find open session for the table
$stmt = $con->prepare("SELECT id FROM table_sessions WHERE table_id = ? AND ended_at IS NULL LIMIT 1");
$stmt->bind_param("i", $table_id);
$stmt->execute();
$stmt->bind_result($session_id);
if (!$stmt->fetch()) {
    $stmt->close();
    $con->close();
    die('No open session for this table.');
}
$stmt->close();

// 2. Create new order in table_orders
$stmt = $con->prepare("INSERT INTO table_orders (table_id, created_by, session_id, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iii", $table_id, $user_id, $session_id);
if (!$stmt->execute()) {
    $stmt->close();
    $con->close();
    die('Failed to create order.');
}
$order_id = $stmt->insert_id;
$stmt->close();

// 3. Add items to order_items
$stmt = $con->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
foreach ($items as $item) {
    $item_id = intval($item['item_id']);
    $quantity = intval($item['quantity']);
    if ($item_id > 0 && $quantity > 0) {
        $stmt->bind_param("iii", $order_id, $item_id, $quantity);
        $stmt->execute();
    }
}
$stmt->close();
$con->close();

// Redirect or success message
header("Location: ../tables.php?success=1");
exit;
?>