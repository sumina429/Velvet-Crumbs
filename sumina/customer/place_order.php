<?php
// place_order.php

include '../config/db.php';
include '../config/session.php';

if (!isset($_SESSION['uid'])) {
    header("Location: ../login.php");
    exit;
}

$uid = $_SESSION['uid'];

// Debug: print input arrays for troubleshooting
echo '<pre>';
echo "\$_GET:\n";
print_r($_GET);
echo "\n\$_POST:\n";
print_r($_POST);
echo '</pre>';

// Get cart summary
$sql_get_summary = "SELECT SUM(c.quantity) as total_items, SUM(c.quantity * p.price) as items_total 
                    FROM cart c JOIN products p ON c.pid = p.pid 
                    WHERE c.uid = {$_SESSION['uid']}";
$result_summary = mysqli_query($db, $sql_get_summary);
$summary_data = mysqli_fetch_assoc($result_summary);

$total_items = $summary_data['total_items'] ?? 0;
$items_total = $summary_data['items_total'] ?? 0;
$tax_amount  = round($items_total * 0.10, 2);
$shipping_fee = isset($_GET['shippingFee']) ? floatval($_GET['shippingFee']) : 0;
$total = $items_total + $tax_amount + $shipping_fee;


$status = "Pending"; // Default status value
$payment_method = isset($_GET['method']) ? $_GET['method'] : "";

// For debug
echo "<h3>Data to insert in orders</h3>";
echo 'uid : ' . $uid . '<br>';
echo 'items_total : ' . $items_total . '<br>';
echo 'shipping_fee : ' . $shipping_fee . '<br>';
echo 'tax : ' . $tax_amount . '<br>';
echo 'total : ' . $total . '<br>';
echo 'status : ' . $status . '<br>';
echo 'payment_method : '. $payment_method . '<br>';

// Get cart items and compute items total and total items
$cart_items = [];
$total_items = 0;
$items_total = 0.0;

$cart_query = $db->prepare("SELECT * FROM cart WHERE uid = ?");
$cart_query->bind_param("i", $uid);
$cart_query->execute();
$cart_result = $cart_query->get_result();

while ($row = $cart_result->fetch_assoc()) {
    $cart_items[] = $row;
    // Get price from products table
    $price_stmt = $db->prepare("SELECT price FROM products WHERE pid = ?");
    $price_stmt->bind_param("i", $row['pid']);
    $price_stmt->execute();
    $price_stmt->bind_result($price);
    if ($price_stmt->fetch()) {
        $items_total += $price * $row['quantity'];
        $total_items += $row['quantity'];
    }
    $price_stmt->close();
}
$cart_query->close();

if (empty($cart_items)) {
    header("Location: cart.php?error=empty_cart");
    exit;
}

echo "<h3>Data from cart items</h3>";
foreach ($cart_items as $item) {
    echo 'pid: ' . $item['pid'] . ', ';
    echo 'quantity: ' . $item['quantity'] . '<br>';
}

// -- orders table schema (from image) --
// oid (auto), uid, shipping_fee, tax, items_total, total, status, payment_method, created_at, updated_at

$order_stmt = $db->prepare("INSERT INTO orders (uid, shipping_fee, tax, items_total, total, status, payment_method, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
$order_stmt->bind_param("iddidss", $uid, $shipping_fee, $tax_amount, $items_total, $total, $status, $payment_method);

if (!$order_stmt->execute()) {
    die("Could not place order: " . $order_stmt->error);
} else {
    echo "order added";
}

$oid = $db->insert_id; // get most recent order id for linking order_items

$order_stmt->close();

// Put all cart items into order_items, linking to $oid
$order_item_stmt = $db->prepare("INSERT INTO order_items (oid, pid, quantity) VALUES (?, ?, ?)");

foreach ($cart_items as $item) {
    $order_item_stmt->bind_param("iii", $oid, $item['pid'], $item['quantity']);
    if(!$order_item_stmt->execute()){
        die("Could not place order items: " . $order_stmt->error);
    } else {
        echo "order items added";
    }
}

$order_item_stmt->close();

// Delete cart for user
$del_cart_stmt = $db->prepare("DELETE FROM cart WHERE uid = ?");
$del_cart_stmt->bind_param("i", $uid);
if(!$del_cart_stmt->execute()){
    die("Could not delete order: " . $order_stmt->error);
} else {
    echo "order deleted";
}
$del_cart_stmt->close();

// Redirect to order success or summary
header("Location: payment_success.php?oid=" . $oid);
exit;
?>

