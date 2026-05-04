<?php
include '../config/db.php';
include '../config/session.php';
isCustomer();

$oid = isset($_GET['oid']) ? intval($_GET['oid']) : 0; // safer

// Fetch all order columns for the given order
$sql_get_detail = "SELECT o.oid, o.uid, o.shipping_fee, o.tax, o.total, o.status, o.payment_method, o.created_at, o.updated_at, o.items_total, u.fname
                   FROM orders o
                   JOIN users u ON o.uid = u.uid
                   WHERE o.oid = $oid
                   LIMIT 1";

$result_get_detail = mysqli_query($db, $sql_get_detail);
$order_data = mysqli_fetch_assoc($result_get_detail);

if (!$order_data) {
    echo "<h2>Order not found.</h2>";
    exit;
}

// Prepare data for display
$items_total  = isset($order_data['items_total']) ? $order_data['items_total'] : 0;
$tax          = isset($order_data['tax']) ? $order_data['tax'] : 0;
$shipping_fee = isset($order_data['shipping_fee']) ? $order_data['shipping_fee'] : 0;
$total        = isset($order_data['total']) ? $order_data['total'] : 0;
$payment_method = $order_data['payment_method'] ?? '—';
$status       = $order_data['status'] ?? 'pending';
$created_at   = $order_data['created_at'] ?? '';
$updated_at   = $order_data['updated_at'] ?? '';
$order_type   = (floatval($shipping_fee) > 0) ? "Delivery" : "Self Collection";
$display_name = htmlspecialchars($order_data['fname']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed Successfully! - Velvet Crumbs</title>
    <link rel="stylesheet" href="cs/payment_success.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="success-card">
    <div class="icon-circle">✓</div>
    <h2 style="text-align:center; margin:0;">Order Placed Successfully!</h2>
    <p style="text-align:center; color:#666; margin:12px 0;">
        Thank you for your order, <strong><?= $display_name ?></strong>
    </p>
    <p style="text-align:center;">
        <span class="order-id-badge">Order ID: <?= $order_data['oid'] ?></span>
    </p>
    <div class="order-details">
        <h4 style="margin:0 0 12px 0; color:#d84b90;">Order Details</h4>
        <div class="detail-row">
            <span>Order Status</span>
            <span><?= htmlspecialchars(ucwords($status)) ?></span>
        </div>
        <div class="detail-row">
            <span>Items Total</span>
            <span>Rs. <?= number_format($items_total, 2) ?></span>
        </div>
        <div class="detail-row">
            <span>Tax</span>
            <span>Rs. <?= number_format($tax, 2) ?></span>
        </div>
        <div class="detail-row">
            <span>Shipping Fee</span>
            <span>Rs. <?= number_format($shipping_fee, 2) ?></span>
        </div>
        <hr style="border:0; border-top:1px solid #eee; margin:12px 0;">
        <div class="detail-row" style="font-weight: bold; font-size: 1.1em;">
            <span>Total Amount</span>
            <b style="color:#d84b90;">Rs. <?= number_format($total, 2) ?></b>
        </div>
        <div class="detail-row" style="margin-top:12px;">
            <span>Payment Method</span>
            <span><?= htmlspecialchars($payment_method) ?></span>
        </div>
        <div class="detail-row">
            <span>Order Type</span>
            <span><?= $order_type ?></span>
        </div>
        <div class="detail-row">
            <span>Placed On</span>
            <span><?= htmlspecialchars(date('d-m-Y H:i', strtotime($created_at))) ?></span>
        </div>
    </div>

    <div class="btn-group">
        <a href="tracking.php?oid=<?= $order_data['oid'] ?>" class="btn-track">Track Order</a>
        <a href="product.php" 
           style="flex:1; text-align:center; padding:12px; border:1px solid #ddd; border-radius:10px; text-decoration:none; color:#666;">
            Continue Shopping
        </a>
    </div>
</div>

</body>
</html>