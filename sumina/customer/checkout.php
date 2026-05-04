<?php
include '../config/db.php';
include '../config/session.php';
isCustomer();

// Get user's info
$user_sql = "SELECT * FROM users WHERE uid = {$_SESSION['uid']}";
$user_result = mysqli_query($db, $user_sql);
$user_data = mysqli_fetch_assoc($user_result);

// Get cart summary
$sql_get_summary = "SELECT SUM(c.quantity) as total_items, SUM(c.quantity * p.price) as items_total 
                    FROM cart c JOIN products p ON c.pid = p.pid 
                    WHERE c.uid = {$_SESSION['uid']}";
$result_summary = mysqli_query($db, $sql_get_summary);
$summary_data = mysqli_fetch_assoc($result_summary);

$total_items = $summary_data['total_items'] ?? 0;
$items_total = $summary_data['items_total'] ?? 0;

// Redirect to product.php if cart is empty
if (!$total_items || $total_items <= 0) {
    header("Location: cart.php");
    exit();
}

$tax_amount  = round($items_total * 0.10, 2);
$delivery_fee = 200; // default – updated by JS
$grand_total = $items_total + $tax_amount + $delivery_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Velvet Crumbs</title>
    <link rel="stylesheet" href="cs/checkout.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        #warning-msg {
            display: none;
            margin: 14px 0 0 0; 
            padding: 10px 12px;
            background: #fbf1f1;
            color: #d84b90;
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #d84b9033;
        }
    </style>
</head>
<body>

<header>
    <div class="brand">
        <img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span>
    </div>
    <nav class="nav-links">
        <a href="../">Home</a>
        <a href="product.php">Products</a>
        <a href="../about.php">About</a>
    </nav>
    <div class="header-actions">
        <div style="position: relative;">
            <a href="cart.php" style="text-decoration:none; color:inherit;">
                <span style="font-size:24px;">🛒</span>
            </a>
            <?php
            $sql_count = "SELECT COUNT(*) as count FROM cart WHERE uid = {$_SESSION['uid']}";
            $res_count = mysqli_query($db, $sql_count);
            $cnt = mysqli_fetch_assoc($res_count)['count'];
            if ($cnt > 0) echo '<span id="cart-badge">'.$cnt.'</span>';
            ?>
        </div>
        <a href="profile.php" class="user-pill">👤 <?= htmlspecialchars($_SESSION['username']) ?></a>
        <a href="../logout.php" class="logout-link">Logout</a>
    </div>
</header>

<div class="dashboard-header">
    <h2>Item Checkout</h2>
</div>

<div class="content-wrapper">
    <a href="cart.php" class="back-link">← Back to Cart</a>
    <h1>Checkout</h1>

    <div class="checkout-layout">
        <div>

            <h3>Delivery Method</h3>
            <div class="method-container" id="delivery-group">
                <div class="method-box active" onclick="updateDelivery(this, 200)">
                    <div style="color:#9c4be0; font-weight:bold;">🚚 Delivery</div>
                    <div style="font-size:12px; color:#888;">Rs. 200</div>
                </div>
                <div class="method-box" onclick="updateDelivery(this, 0)">
                    <div style="font-weight:bold;">Self Collecting</div>
                    <div style="font-size:12px; color:#888;">Free</div>
                </div>
            </div>

            <h3>Contact Information</h3>
            <div class="form-group">
                <input type="text" id="custName" placeholder="Full Name *" required value="<?= htmlspecialchars($user_data['fname'] ?? '') ?>" oninput="checkContactInfo()">
                <input type="email" id="custEmail" placeholder="Email *" required value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" oninput="checkContactInfo()">
                <input type="text" id="custPhone" placeholder="Phone *" required value="<?= htmlspecialchars($user_data['contact'] ?? '') ?>" oninput="checkContactInfo()">
                <textarea id="custAddr" placeholder="Address / Additional Notes" rows="3" oninput="checkContactInfo()"><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
            </div>
            <div id="warning-msg">Please fill the above form to continue.</div>

            <div id="payment-method-section" style="display:none;">
                <h3>Payment Method</h3>
                <div class="method-container" id="payment-group">
                    <div 
                        class="method-box" 
                        data-url="./onlinepayment.php" 
                        onclick="selectPayment(this, 'payment-group')"
                    >💳 Online Payment</div>
                    <div 
                        class="method-box active" 
                        data-url="" 
                        onclick="selectPayment(this, 'payment-group')"
                    >💵 Cash on Delivery</div>
                </div>
                <button class="place-order-btn" onclick="handlePlaceOrder()">Place Order</button>
            </div>

            <div id="form-incomplete-warning" style="color:#d7263d; background:#ffe3e3; border:1px solid #fd8888; border-radius:4px; padding:6px 9px; margin:18px 0 0 0; font-weight:semibold; text-align:center; display:none;">
                Please fill out all contact information fields to proceed.
            </div>
        </div>

        <div class="summary-mini" id="order-summary">
            <h2 style="color:#d84b90; margin-top:0;">Order Summary</h2>
            <div class="summary-row"><span>Total Items</span><span id="items-total"><?= $total_items ?></span></div>
            <div class="summary-row"><span>Items Total</span><span id="items-total-amount">Rs. <?= number_format($items_total, 2) ?></span></div>
            <div class="summary-row"><span>Tax (10%)</span><span id="tax-amount">Rs. <?= number_format($tax_amount, 2) ?></span></div>
            <div class="summary-row"><span>Delivery</span><span id="delivery-display">Rs. <?= number_format($delivery_fee, 2) ?></span></div>
            <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">
            <div class="summary-row" style="align-items:center;">
                <h2 style="margin:0;">Total</h2>
                <h2 style="margin:0; color:#d84b90;" id="grand-total">Rs. <?= number_format($grand_total, 2) ?></h2>
                <input type="hidden" id="total-amount" value="<?= number_format($grand_total, 2) ?>">
            </div>
        </div>
    </div>
</div>

<script>
let itemsTotal   = <?= floatval($items_total) ?>;
let totalItems   = <?= intval($total_items) ?>;
let deliveryFee  = <?= $delivery_fee ?>;
const taxRate    = 0.10;

function refreshTotals() {
    deliveryFee = getSelectedDeliveryFee();
    let tax   = Number((itemsTotal * taxRate).toFixed(2));
    let grand = Number((itemsTotal + tax + deliveryFee).toFixed(2));

    document.getElementById('items-total').textContent         = totalItems;
    document.getElementById('items-total-amount').textContent  = "Rs. " + itemsTotal.toFixed(2);
    document.getElementById('tax-amount').textContent          = "Rs. " + tax.toFixed(2);
    document.getElementById('delivery-display').textContent    = "Rs. " + deliveryFee.toFixed(2);
    document.getElementById('grand-total').textContent         = "Rs. " + grand.toFixed(2);
}

// Utility to check if all required contact info fields are filled
function checkContactInfo() {
    const name  = document.getElementById('custName').value.trim();
    const email = document.getElementById('custEmail').value.trim();
    const phone = document.getElementById('custPhone').value.trim();
    const addr  = document.getElementById('custAddr').value.trim();

    const pmSection = document.getElementById('payment-method-section');
    const warnMsg   = document.getElementById('warning-msg');

    if (name && email && phone && addr) {
        pmSection.style.display = '';
        warnMsg.style.display = 'none';
    } else {
        pmSection.style.display = 'none';
        warnMsg.style.display = '';
    }
}

// Modify selectPayment and PlaceOrder to ensure new logic works if called directly (defensive)
function selectPayment(el, groupId) {
    // Do not allow if not filled
    if (!isContactInfoFilled()) {
        checkContactInfo();
        return false;
    }
    document.querySelectorAll(`#${groupId} .method-box`).forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    const url = el.dataset.url;
    if (url && url.trim() !== '') {
        const name  = document.getElementById('custName').value.trim();
        const email = document.getElementById('custEmail').value.trim();
        const phone = document.getElementById('custPhone').value.trim();
        const addr  = document.getElementById('custAddr').value.trim();
        const total = document.getElementById('total-amount').value;
        setTimeout(() => {
            const params = new URLSearchParams({
                method: "Online Payment",
                custName: name,
                custEmail: email,
                custPhone: phone,
                custAddr: addr,
                shippingFee: deliveryFee,
                amount: total
            }).toString();
            window.location.href = `onlinepayment.php?${params}`;
        }, 300);
    }
}

function handlePlaceOrder() {
    const name  = document.getElementById('custName').value.trim();
    const email = document.getElementById('custEmail').value.trim();
    const phone = document.getElementById('custPhone').value.trim();
    const addr  = document.getElementById('custAddr').value.trim();

    if (!name || !email || !phone || !addr) {
        checkContactInfo();
        return;
    }

    const activePaymentBox = document.querySelector('#payment-group .method-box.active');
    const isOnline = activePaymentBox && activePaymentBox.innerText.includes('Online Payment');

    if (isOnline) {
        alert("Please complete the online payment process.");
        return;
    }

    // Cash on Delivery
    const grandEl = document.getElementById('grand-total');
    const amount  = grandEl.textContent.replace('Rs. ', '').replace(/,/g, '').trim();
    const shipping = getSelectedDeliveryFee();

    // Pass all customer fields (custName, custEmail, custPhone, custAddr) to place_order.php as query parameters
    const params = new URLSearchParams({
        method: "COD",
        custName: name,
        custEmail: email,
        custPhone: phone,
        custAddr: addr,
        shippingFee: deliveryFee
    }).toString();
    window.location.href = `place_order.php?${params}`;
}

function getSelectedDeliveryFee() {
    const active = document.querySelector('#delivery-group .method-box.active');
    return active && active.innerText.includes('Self Collecting') ? 0 : 200;
}

function updateDelivery(el, fee) {
    document.querySelectorAll('#delivery-group .method-box').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    refreshTotals();
}

// Helper to check if all required for defensive use
function isContactInfoFilled() {
    const name  = document.getElementById('custName').value.trim();
    const email = document.getElementById('custEmail').value.trim();
    const phone = document.getElementById('custPhone').value.trim();
    const addr  = document.getElementById('custAddr').value.trim();
    return name && email && phone && addr;
}

// On page load, check contact info and refresh totals
window.onload = function() {
    checkContactInfo();
    refreshTotals();
};
</script>
</body>
</html>