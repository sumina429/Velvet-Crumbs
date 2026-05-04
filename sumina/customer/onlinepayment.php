<?php
include '../config/db.php';
include '../config/session.php';
isCustomer();

$amount   = isset($_GET['amount'])   ? floatval($_GET['amount'])   : 0.00;
$shipping = isset($_GET['shipping']) ? floatval($_GET['shipping']) : 200;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Payment - Velvet Crumbs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="cs/onlinepayment.css">
</head>
<body>

<header>
    <div class="brand"><img src="../logo.png"><span>Velvet Crumbs</span></div>
    <div class="header-actions">
        <div style="position: relative;">
            <a href="cart.php" style="text-decoration:none; color:inherit;"><span style="font-size:24px;">🛒</span></a>
            <?php
            $q = mysqli_query($db, "SELECT COUNT(*) c FROM cart WHERE uid={$_SESSION['uid']}");
            $c = mysqli_fetch_assoc($q)['c'];
            if ($c) echo "<span id='cart-badge'>$c</span>";
            ?>
        </div>
        <a href="profile.php" class="user-pill">👤 <?= htmlspecialchars($_SESSION['username']) ?></a>
        <a href="../logout.php" class="logout-link">Logout</a>
    </div>
</header>

<section class="hero-banner">
    <h1>Online Payment</h1>
</section>

<div class="container">
    <div class="payment-grid">
        <div class="app-card" onclick="openModal('esewa')">
            <img src="../esewa.png" alt="eSewa">
            <p>eSewa</p>
        </div>
        <div class="app-card" onclick="openModal('khalti')">
            <img src="../khalti.png" alt="Khalti">
            <p>Khalti</p>
        </div>
    </div>
</div>

<div class="modal-overlay" id="paymentModal">
    <div class="login-card" id="modalCard">
        <div class="close-x" onclick="closeModal()">✕</div>
        <div class="curve"></div>

        <div class="login-body">
            <div class="inner-box">
                <img id="providerLogo" style="width:120px; margin-bottom:15px;">

                <div id="loginStage">
                    <input type="text" placeholder="ID / Mobile" id="loginId">
                    <input type="password" placeholder="Password / PIN" id="loginPin">
                    <button class="btn-action" onclick="showPayment()">Login</button>
                </div>

                <div id="paymentStage" style="display:none;">
                    <input type="number" id="payAmount" placeholder="Amount" value="<?= number_format($amount, 2) ?>" readonly>
                    <p style="font-size:14px; color:#666; margin:5px 0;">
                        Total to pay: <b style="color:#d84b90;">Rs. <?= number_format($amount, 2) ?></b>
                    </p>
                    <button class="btn-action" id="confirmPayBtn">Confirm & Pay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add a hidden input to keep the original full query string (excluding the page) for passing along -->
<?php
// Rebuild the current query string (everything after the initial '?') for passing to next page
$query_string = $_SERVER['QUERY_STRING'];
?>
<input type="hidden" id="fullQueryString" value="<?= htmlspecialchars($query_string) ?>">

<script>
const modal        = document.getElementById('paymentModal');
const card         = document.getElementById('modalCard');
const logo         = document.getElementById('providerLogo');
const loginStage   = document.getElementById('loginStage');
const paymentStage = document.getElementById('paymentStage');
const confirmBtn   = document.getElementById('confirmPayBtn');

function openModal(type) {
    modal.classList.add('active');
    loginStage.style.display = 'block';
    paymentStage.style.display = 'none';

    if (type === 'esewa') {
        card.className = 'login-card theme-esewa';
        logo.src = '../esewa.png';
    } else {
        card.className = 'login-card theme-khalti';
        logo.src = '../khalti.png';
    }
}

function closeModal() {
    modal.classList.remove('active');
}

function showPayment() {
    if (!document.getElementById('loginId').value || !document.getElementById('loginPin').value) {
        alert("Please enter ID/Mobile and PIN");
        return;
    }
    loginStage.style.display = 'none';
    paymentStage.style.display = 'block';
}

function goToSuccess() {
    closeModal();

    // Get the original query string from the hidden input
    const fullQuery = document.getElementById('fullQueryString').value;
    // Go to payment success page, passing the same query string as received
    setTimeout(() => {
        window.location.href = `place_order.php?${fullQuery}`;
    }, 600);
}

confirmBtn.addEventListener('click', goToSuccess);
</script>

</body>
</html>