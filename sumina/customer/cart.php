<?php

include '../config/db.php';
include '../config/session.php';
isCustomer();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Velvet Crumbs</title>
    <link rel="stylesheet" href="./cs/cart.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">


</head>

<body>

    <!-- Header -->
    <header>
        <div class="brand"><img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span></div>
        <div>

            <!-- Navigation -->
            <nav class="nav-links">
                <a href="../">Home</a>
                <a href="product.php">Products</a>
                <a href="../about.php">About</a>
            </nav>
        </div>

        <div class="header-actions">
            <div style="position: relative;">
                <a href="cart.php" style="text-decoration: none; color: inherit;">
                    <span style="font-size: 24px;">🛒</span>
                </a>

                <?php
                $sql_get_count = "SELECT count(*) as count FROM cart c JOIN products p ON c.pid = p.pid WHERE c.uid = {$_SESSION['uid']}";
                $result = mysqli_query($db, $sql_get_count);
                $data_count = mysqli_fetch_assoc($result);
                $count = $data_count['count'];

                if ($count) {
                    echo '<span id="cart-badge">' . $count . '</span>';
                }
                ?>
            </div>
            <a href="profile.php" class="user-pill">👤
                <?php echo $_SESSION['username']; ?>
            </a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </header>

    <div class="dashboard-header">
        <h2 style="margin:0; font-size: 28px;">Customer Cart</h2>
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-bar" placeholder="Search for cupcakes, doughnuts...">
        </div>
    </div>

    <div class="main-container">
        <div>
            <a href="product.php" class="back-link">← Back to Product</a>
            <h1 style="color: #d81b60; margin: 0 0 25px 0;">Shopping Cart</h1>

            <?php
            $sql = "SELECT * FROM cart WHERE uid = {$_SESSION['uid']}";
            $result = mysqli_query($db, $sql);
            while ($cart = mysqli_fetch_assoc($result)) {
                $sql = "SELECT * FROM products WHERE pid = {$cart['pid']}";
                $result2 = mysqli_query($db, $sql);
                $product = mysqli_fetch_assoc($result2);
                ?>
                <div class="cart-card" data-price="<?php echo $product['price']; ?>"
                    data-cart-id="<?php echo $cart['cart_id']; ?>">
                    <div class="product-sq">
                        <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['p_name']; ?>">
                    </div>
                    <div class="item-details">
                        <h3><?php echo $product['p_name']; ?></h3>
                        <div class="qty-row">
                            <button class="qty-btn" onclick="updateItem(this, -1)">-</button>
                            <span class="qty-val"><?php echo $cart['quantity']; ?></span>
                            <button class="qty-btn" onclick="updateItem(this, 1)">+</button>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size:12px; color:#888;">Rs. <?php echo $product['price']; ?></span><br>
                        <strong class="item-total">Rs. <?php echo $product['price'] * $cart['quantity']; ?></strong><br>
                        <a class="remove-icon"
                            href="remove_from_cart.php?cart_id=<?php echo $cart['cart_id']; ?>&pid=<?php echo $product['pid']; ?>"
                            onclick="removeItem(this)">🗑️</a>
                        <!-- <button class="remove-icon" onclick="removeItem(this)">🗑️</button> -->
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <?php
        $sql_get_subtotal = "SELECT SUM(c.quantity * p.price) AS total_price_sum FROM cart c JOIN products p ON c.pid = p.pid WHERE c.uid = {$_SESSION['uid']}";
        $result = mysqli_query($db, $sql_get_subtotal);
        $data = mysqli_fetch_assoc($result);
        $sub_total = $data['total_price_sum'];
        $tax = $sub_total * 0.1;
        ?>

        <?php if ($sub_total > 0): ?>
        <div class="summary-card" style="margin-top: 1.5rem;">
            <h2 style="margin-top: 0;">Order Summary</h2>
            <div class="summary-row"><span>Subtotal</span><span id="subtotal">Rs. <?php echo $sub_total; ?></span></div>
            <div class="summary-row"><span>Tax (10%)</span><span id="tax">Rs. <?php echo $tax ?></span></div>
            <div class="total-divider"></div>
            <div class="total-row">
                <span class="total-label">Total</span>
                <span class="final-total-val" id="final-total">Rs. <?php echo $sub_total + $tax; ?></span>
            </div>
            <button class="checkout-btn" onclick="window.location.href='checkout.php'">Proceed to Checkout</button>
        </div>
        <?php else: ?>
        <div class="summary-card mt-6" style="margin-top: 1.5rem;">
            <h2 style="margin-top: 0;">Order Summary</h2>
            <div class="text-center" style="padding: 30px; color: #888; font-size: 1.2em;">
                No items in cart.
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function updateItem(btn, change) {
            const card = btn.closest('.cart-card');
            const qtySpan = card.querySelector('.qty-val');
            const price = parseFloat(card.getAttribute('data-price'));
            const cartId = card.getAttribute('data-cart-id');
            let qty = parseInt(qtySpan.innerText) + change;
            if (qty < 1) qty = 1;
            qtySpan.innerText = qty;
            card.querySelector('.item-total').innerText = "Rs. " + (qty * price).toFixed(2);
            calculateGrandTotal();
            // Dynamically update the quantity in the database
            updateCartQtyInDb(cartId, qty);
        }

        function updateCartQtyInDb(cartId, quantity) {
            // Sends an AJAX POST request to update_cat_qty.php
            fetch('update_cat_qty.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `cart_id=${cartId}&quantity=${quantity}`
            })
                .then(response => response.text())
                .then(data => {
                    // Optionally, handle server response
                    // You could check for "success" or show a message if desired
                    // console.log(data);
                })
                .catch(error => {
                    alert('Failed to update cart item.');
                });
        }

        function calculateGrandTotal() {
            let subtotal = 0;
            const cards = document.querySelectorAll('.cart-card');
            cards.forEach(card => {
                const price = parseFloat(card.getAttribute('data-price'));
                const qty = parseInt(card.querySelector('.qty-val').innerText);
                subtotal += (price * qty);
            });
            const tax = subtotal * 0.10;
            const total = subtotal + tax;
            document.getElementById('subtotal').innerText = "Rs. " + subtotal.toFixed(2);
            document.getElementById('tax').innerText = "Rs. " + tax.toFixed(2);
            document.getElementById('final-total').innerText = "Rs. " + total.toFixed(2);

            // Badge logic: Number of unique varieties
            document.getElementById('cart-badge').innerText = cards.length;
        }

        function removeItem(btn) {
            btn.closest('.cart-card').remove();
            calculateGrandTotal();
        }
    </script>
</body>

</html>