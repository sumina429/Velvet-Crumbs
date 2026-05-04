<?php
include "config/db.php";
include "config/session.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Velvet Crumbs</title>
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
            <img src="logo.png" alt="Velvet Crumbs Logo">
            <span>Velvet Crumbs</span>
        </div>
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] == "customer") {
            ?>
            <!-- Navigation -->
            <div>
                <nav class="nav-links">
                    <a href="#">Home</a>
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
                    <a href="#">Home</a>
                    <a href="about.php">About</a>                    <a href="contact.php">Contact</a>                </nav>
            </div>
            <div class="header-actions">
                <button onclick="window.location.href='admin_register.php'" class="btn btn-vendor">Become a Vendor</button>
                <button onclick="window.location.href='login.php'" class="btn btn-login">Login</button>
            </div>
        <?php } ?>
    </header>

    <section class="hero">
        <h1>Welcome to Velvet Crumbs</h1>
        <p>Indulge in our artisan cupcakes and doughnuts</p>
        <div class="search-box">
            <input type="text" placeholder="Search for the items" />
        </div>
    </section>


    <main class="main-content">
        <div class="section-title"> Products
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "customer") { ?>
                <a href="customer/product.php"
                    style="font-size: 18px; font-weight: 400; opacity: 0.5; margin-left: 10px;">more Products...</a>
            <?php } ?>
        </div>

        <div class="product-grid">
            <?php
            // Fetch any 3 products from the database
            $sql = "SELECT * FROM products LIMIT 3";
            $result = mysqli_query($db, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Use safe default image if not present, and escape output
                    $img = !empty($row['image']) ? "images/" . htmlspecialchars($row['image']) : "imagez/noimg.png";
                    $category = htmlspecialchars($row['category']);
                    $p_name = htmlspecialchars($row['p_name']);
                    $desc = htmlspecialchars($row['description']);
                    $price = htmlspecialchars($row['price']);
                    $pid = intval($row['pid']);
                    // Example stats (if not in db, could be left blank or removed)
                    $views = isset($row['views']) ? htmlspecialchars($row['views']) : '--';
                    $stock = isset($row['stock']) ? htmlspecialchars($row['stock']) : '--';


                    if (isset($_SESSION['role']) && $_SESSION['role'] === "customer" && isset($_SESSION['uid'])) {
                        $add_card_link = "customer/add_to_cart.php?pid={$row['pid']}&uid={$_SESSION['uid']}";
                    } else {
                        $add_card_link = "login.php";
                    }

                    echo '<div class="card">
                        <div class="img-wrap">
                            <span class="card-badge">' . $category . '</span>
                            <img src="' . $img . '" alt="' . $p_name . '">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">' . $p_name . '</h3>
                            <p class="card-text">' . $desc . '</p>
                            <div class="card-stats">
                                <span>👁️ ' . $views . '</span>
                                <span>📦 ' . $stock . ' stock</span>
                            </div>
                            <div class="price">Rs ' . $price . '</div>
                            <div class="card-btn">
                                <button class="view-btn" onclick="window.location.href=\'customer/productdetail.php?pid=' . $pid . '\'">View Details</button>
                                <a class="add_cart" href="' . $add_card_link . '">Add to cart</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div>No products found.</div>';
            }
            ?>
        </div>

        <section class="about-section">
            <div class="about-wrap">
                <h2>About Velvet Crumbs</h2>
                <p class="lead">Artisan cupcakes and doughnuts, baked with care in Kathmandu.</p>
                <p>
                    Velvet Crumbs started from a simple idea: dessert should feel special every time.
                    We bake in small batches so texture and flavor stay consistent—from classic cupcakes to creative seasonal doughnuts.
                </p>
                <a href="about.php" class="btn-about">Learn More</a>
            </div>
        </section>

        <footer>
            <div class="footer-container">
                <div class="footer-column">
                    <h3>Velvet Crumbs</h3>
                    <ul>
                        <li>📍 Kathmandu, Nepal</li>
                        <li>✉️ official@velvetcrumbs.com</li>
                        <li>📞 (123) 456-7890</li>
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