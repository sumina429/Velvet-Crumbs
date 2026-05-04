<?php
include '../config/db.php';
include '../config/session.php';
isCustomer();

$oid = isset($_GET['oid']) ? (int)$_GET['oid'] : 0;

if ($oid <= 0) {
    header("Location: cart.php");
    exit;
}

$sql = "
    SELECT 
        o.oid,
        o.total AS grand_total,
        o.shipping_fee,
        o.tax,
        o.payment_method,
        o.status,
        o.created_at,
        u.fname,
        u.contact AS phone,
        u.email,
        u.address
    FROM orders o
    LEFT JOIN users u ON o.uid = u.uid
    WHERE o.oid = ?
";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $oid);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();


if (!$order) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Not Found - Velvet Crumbs</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="./cs/cart.css">
        <style>
            body { font-family: 'Poppins', sans-serif; background:#f9f9f9; color:#333; margin:0; }
            header { background:white; border-bottom:1px solid #eee; padding:16px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.04); }
            .brand { display:flex; align-items:center; gap:12px; }
            .brand img { height:48px; }
            .brand span { font-size:1.5rem; font-weight:600; color:#d84b90; }
            .nav-links a { margin:0 20px; color:#444; text-decoration:none; font-weight:500; }
            .nav-links a:hover { color:#d84b90; }
            .header-actions { display:flex; align-items:center; gap:24px; }
            .user-pill { background:#d84b90; color:white; padding:8px 20px; border-radius:999px; font-weight:500; text-decoration:none; }
            .logout-link { color:#e53e3e; font-weight:500; text-decoration:none; }
            .cart-badge {
                position:absolute; top:-8px; right:-8px; background:#ef4444; color:white;
                font-size:0.75rem; font-weight:bold; width:20px; height:20px; border-radius:50%;
                display:flex; align-items:center; justify-content:center;
            }
            .error-container {
                max-width:520px; margin:120px auto 40px; padding:48px 32px;
                background:white; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.08);
                text-align:center;
            }
            .error-title { color:#d84b90; font-size:2.2rem; margin:0 0 16px; }
            .error-text { color:#555; font-size:1.05rem; line-height:1.6; margin-bottom:32px; }
            .btn-back {
                display:inline-block; padding:14px 40px; background:#d84b90; color:white;
                font-weight:500; border-radius:12px; text-decoration:none; transition:0.2s;
            }
            .btn-back:hover { background:#c13f7e; transform:translateY(-2px); }
        </style>
    </head>
    <body>

    <header>
        <div class="brand">
            <img src="../logo.png" alt="logo">
            <span>Velvet Crumbs</span>
        </div>
        <nav class="nav-links">
            <a href="../">Home</a>
            <a href="product.php">Products</a>
            <a href="../about.php">About</a>
        </nav>
        <div class="header-actions">
            <div style="position:relative;">
                <a href="cart.php" style="text-decoration:none; color:inherit;">
                    <span style="font-size:24px;">🛒</span>
                </a>
                <?php
                $q = mysqli_query($db, "SELECT COUNT(*) c FROM cart WHERE uid={$_SESSION['uid']}");
                $c = mysqli_fetch_assoc($q)['c'];
                if ($c) echo "<span id='cart-badge'>$c</span>";
                ?>
            </div>
            <a href="profile.php" class="user-pill">👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?></a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </header>

    <div class="error-container">
        <h2 class="error-title">Order Not Found</h2>
        <p class="error-text">
            No order found with ID #<?= $oid ?>.<br>
            Please check the link or contact support.
        </p>
        <a href="cart.php" class="btn-back">Back to Cart</a>
    </div>

    </body>
    </html>
    <?php
    exit;
}


$items_sql = "
    SELECT p.p_name, p.image, oi.quantity, p.price
    FROM order_items oi
    JOIN products p ON oi.pid = p.pid
    WHERE oi.oid = ?
";
$item_stmt = $db->prepare($items_sql);
$item_stmt->bind_param("i", $oid);
$item_stmt->execute();
$items_result = $item_stmt->get_result();
$order_items = $items_result->fetch_all(MYSQLI_ASSOC);
$item_stmt->close();

// Calculations
$subtotal = round(($order['grand_total'] - $order['shipping_fee'] - $order['tax']), 2);

// Status mapping
$status_map = [
    'pending'     => ['Ordered',    0, '#f59e0b'],
    'processing'  => ['Packed',     1, '#3b82f6'],
    'shipped'     => ['Shipped',    2, '#10b981'],
    'delivered'   => ['Delivered',  3, '#059669'],
    'cancelled'   => ['Cancelled', -1, '#ef4444']
];

$raw_status   = strtolower($order['status'] ?? 'pending');
$current_step = $status_map[$raw_status][1] ?? 0;
$status_name  = $status_map[$raw_status][0] ?? 'Pending';
$status_color = $status_map[$raw_status][2] ?? '#6b7280';

$timeline = [
    ['Ordered',    'Order received & confirmed',               $order['created_at']],
    ['Packed',     'Preparing your items',                     null],
    ['Shipped',    'Order handed over to delivery partner',    null],
    ['Delivered',  'Order delivered successfully',             null]
];

if ($current_step === -1) {
    $timeline = [
        ['Ordered',   'Order received', $order['created_at']],
        ['Cancelled', 'Order has been cancelled', date('Y-m-d H:i:s')]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?= $oid ?> - Velvet Crumbs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./cs/cart.css">
    <style>
        :root {
            --pink: #d84b90;
            --dark: #2d1a3e;
            --gray: #6b7280;
            --light: #f9fafb;
            --border: #e5e7eb;
        }
        body { font-family: 'Poppins', sans-serif; background:var(--light); color:#333; margin:0; }
        header { background:white; border-bottom:1px solid var(--border); padding:16px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.04); }
        .brand { display:flex; align-items:center; gap:12px; }
        .brand img { height:48px; }
        .brand span { font-size:1.5rem; font-weight:600; color:var(--pink); }
        .nav-links a { margin:0 20px; color:#444; text-decoration:none; font-weight:500; }
        .nav-links a:hover { color:var(--pink); }
        .header-actions { display:flex; align-items:center; gap:24px; }
        .user-pill { background:var(--pink); color:white; padding:8px 20px; border-radius:999px; font-weight:500; text-decoration:none; }
        .logout-link { color:#e53e3e; font-weight:500; text-decoration:none; }
        .cart-badge {
            position:absolute; top:-8px; right:-8px; background:#ef4444; color:white;
            font-size:0.75rem; font-weight:bold; width:20px; height:20px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
        }
        main { max-width:1100px; margin:40px auto; padding:0 20px; }
        h1 { font-size:2.4rem; margin:0 0 8px; color:var(--dark); }
        .meta { color:var(--gray); margin-bottom:32px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:48px; }
        .card { background:white; border-radius:16px; padding:28px; box-shadow:0 6px 24px rgba(0,0,0,0.06); }
        .card h3 { margin:0 0 20px; color:var(--pink); font-size:1.4rem; }
        .detail-row { display:flex; justify-content:space-between; margin-bottom:12px; color:#444; }
        .grand-total { font-size:1.2rem; font-weight:600; color:var(--pink); margin-top:16px; padding-top:16px; border-top:1px solid var(--border); }
        .items-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:48px; }
        .item-card { background:white; border-radius:12px; padding:16px; box-shadow:0 4px 16px rgba(0,0,0,0.05); display:flex; gap:16px; }
        .item-card img { width:90px; height:90px; object-fit:cover; border-radius:10px; }
        .timeline { position:relative; max-width:760px; margin:0 auto 60px; }
        .timeline::before { content:''; position:absolute; width:4px; background:#e5e7eb; top:12px; bottom:12px; left:20px; }
        .step { position:relative; padding-left:64px; margin-bottom:48px; }
        .step-circle {
            width:44px; height:44px; background:white; border:3px solid #d1d5db; border-radius:50%;
            position:absolute; left:0; top:0; z-index:1; display:flex; align-items:center; justify-content:center;
            font-weight:bold; color:#9ca3af;
        }
        .step.completed .step-circle { background:var(--pink); border-color:var(--pink); color:white; }
        .step.active .step-circle { background:white; border-color:var(--pink); box-shadow:0 0 0 6px rgba(216,75,144,0.25); color:var(--pink); }
        .step-title { font-weight:600; font-size:1.15rem; margin-bottom:6px; }
        .step-desc { color:var(--gray); margin-bottom:8px; }
        .step-time { color:#9ca3af; font-size:0.9rem; }
        .actions { text-align:center; }
        .btn { padding:14px 48px; border-radius:12px; font-weight:500; text-decoration:none; display:inline-block; margin:0 12px; }
        .btn-primary { background:var(--pink); color:white; }
        .btn-primary:hover { background:#c13f7e; }
        .btn-outline { background:white; border:2px solid #d1d5db; color:#444; }
        .btn-outline:hover { border-color:var(--pink); color:var(--pink); }
        .status-badge { padding:6px 18px; border-radius:999px; font-weight:500; font-size:1rem; }
    </style>
</head>
<body>

<header>
    <div class="brand">
        <img src="../logo.png" alt="logo">
        <span>Velvet Crumbs</span>
    </div>
    <nav class="nav-links">
        <a href="../">Home</a>
        <a href="product.php">Products</a>
        <a href="../about.php">About</a>
    </nav>
    <div class="header-actions">
        <div style="position:relative;">
            <a href="cart.php" style="text-decoration:none; color:inherit;">
                <span style="font-size:24px;">🛒</span>
            </a>
            <?php
            $q = mysqli_query($db, "SELECT COUNT(*) c FROM cart WHERE uid={$_SESSION['uid']}");
            $c = mysqli_fetch_assoc($q)['c'];
            if ($c) echo "<span id='cart-badge'>$c</span>";
            ?>
        </div>
        <a href="profile.php" class="user-pill">👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?></a>
        <a href="../logout.php" class="logout-link">Logout</a>
    </div>
</header>

<main>
    <h1>Order #<?= $oid ?></h1>
    <div class="meta">
        Placed on <?= date('d M Y • h:i A', strtotime($order['created_at'])) ?>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3>Order Details</h3>
            <div class="detail-row">
                <span>Status</span>
                <span>
                    <span class="status-badge" style="background:<?= $status_color ?>22; color:<?= $status_color ?>">
                        <?= htmlspecialchars($status_name) ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span>Payment Method</span>
                <span><?= htmlspecialchars($order['payment_method'] ?: '—') ?></span>
            </div>
            <div class="detail-row">
                <span>Order Type</span>
                <span><?= $order['shipping_fee'] > 0 ? 'Delivery' : 'Self Collection' ?></span>
            </div>
            <?php if ($order['shipping_fee'] > 0): ?>
            <div class="detail-row">
                <span>Delivery Address</span>
                <span><?= htmlspecialchars($order['address'] ?: '—') ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>Payment Summary</h3>
            <div class="detail-row">
                <span>Items Subtotal</span>
                <span>Rs. <?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="detail-row">
                <span>Tax (10%)</span>
                <span>Rs. <?= number_format($order['tax'], 2) ?></span>
            </div>
            <div class="detail-row">
                <span>Shipping Fee</span>
                <span>Rs. <?= number_format($order['shipping_fee'], 2) ?></span>
            </div>
            <div class="grand-total detail-row">
                <span>Total Amount</span>
                <span>Rs. <?= number_format($order['grand_total'], 2) ?></span>
            </div>
        </div>
    </div>

    <h2 style="margin:0 0 24px;">Your Items (<?= count($order_items) ?>)</h2>
    <div class="items-grid">
        <?php foreach ($order_items as $item): ?>
        <div class="item-card">
            <img src="../images/<?= htmlspecialchars($item['image'] ?? 'placeholder.jpg') ?>" alt="">
            <div>
                <div style="font-weight:500; margin-bottom:6px;"><?= htmlspecialchars($item['p_name']) ?></div>
                <div style="color:var(--gray);">
                    Qty: <?= $item['quantity'] ?> × Rs. <?= number_format($item['price'], 2) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin:0 0 28px;">Order Progress</h2>
    <div class="timeline">
        <?php foreach ($timeline as $index => $step):
            $step_name = $step[0];
            $desc      = $step[1];
            $time      = $step[2] ? date('d M Y • h:i A', strtotime($step[2])) : '—';

            $is_completed = $current_step > $index;
            $is_active    = $current_step === $index;
            $class = $is_completed ? 'completed' : ($is_active ? 'active' : '');
            if ($current_step === -1 && $step_name === 'Cancelled') $class .= ' active';
        ?>
        <div class="step <?= $class ?>">
            <div class="step-circle"><?= $is_completed ? '✓' : ($is_active ? '→' : '') ?></div>
            <div>
                <div class="step-title"><?= $step_name ?></div>
                <div class="step-desc"><?= $desc ?></div>
                <div class="step-time"><?= $time ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <a href="product.php" class="btn btn-primary">Continue Shopping</a>
        <a href="cart.php" class="btn btn-outline">Back to Cart</a>
    </div>
</main>

</body>
</html>