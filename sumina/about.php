<?php
include "config/db.php";
include "config/session.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us — Velvet Crumbs</title>
    <link rel="stylesheet" href="./index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>

    <header>
        <div class="brand">
            <a href="./" style="display:flex;align-items:center;text-decoration:none;color:inherit;gap:8px;">
                <img src="logo.png" alt="Velvet Crumbs Logo">
                <span>Velvet Crumbs</span>
            </a>
        </div>
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] == "customer") {
            ?>
            <div>
                <nav class="nav-links">
                    <a href="./">Home</a>
                    <a href="customer/product.php">Products</a>
                    <a href="about.php">About</a>
                    <a href="contact.php">Contact</a>
                </nav>
            </div>
            <div class="header-actions">
                <div style="position: relative;">
                    <a href="customer/cart.php" style="text-decoration: none; color: inherit;">
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
                <a href="customer/profile.php" class="user-pill">👤
                    <?php echo $_SESSION['username']; ?>
                </a>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        <?php } else { ?>
            <div>
                <nav class="nav-links">
                    <a href="./">Home</a>
                    <a href="about.php">About</a>
                    <a href="contact.php">Contact</a>
                </nav>
            </div>
            <div class="header-actions">
                <button onclick="window.location.href='admin_register.php'" class="btn btn-vendor">Become a Vendor</button>
                <button onclick="window.location.href='login.php'" class="btn btn-login">Login</button>
            </div>
        <?php } ?>
    </header>

    <main class="main-content">
        <div class="about-wrap">
            <h1>About Velvet Crumbs</h1>
            <p class="lead">Artisan cupcakes and doughnuts, baked with care in Kathmandu.</p>

            <h2>Our story</h2>
            <p>
                Velvet Crumbs started from a simple idea: dessert should feel special every time.
                We bake in small batches so texture and flavor stay consistent—from classic cupcakes to creative seasonal doughnuts.
            </p>

            <h2>What we care about</h2>
            <p>
                Quality ingredients, straightforward recipes, and orders that arrive as fresh as when they left our kitchen.
                Whether you are treating yourself or sending a gift, we want every bite to feel worth it.
            </p>

            <h2>Visit &amp; contact</h2>
            <p>
                📍 Kathmandu, Nepal<br>
                ✉️ official@velvetcrumbs.com<br>
                📞 (123) 9860022789
            </p>
        </div>

        <footer>
            <div class="footer-container">
                <div class="footer-column">
                    <h3>Velvet Crumbs</h3>
                    <ul>
                        <li>📍 Kathmandu, Nepal</li>
                        <li>✉️ official@velvetcrumbs.com</li>
                        <li>📞 (123) 9860022789</li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Explore</h3>
                    <ul>
                        <li><a href="customer/product.php">Products</a></li>
                        <li><a href="about.php">About us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="terms.php">Terms</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Join the Club</h3>
                    <p class="newsletter-desc">Fresh treats in your inbox.</p>
                    <form class="join-form">
                        <input type="email" placeholder="Email" required>
                        <button type="submit" class="btn-join">Join</button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; 2026 Velvet Crumbs. All rights reserved.
            </div>
        </footer>
    </main>

</body>

</html>
