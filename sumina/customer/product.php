<?php

include '../config/db.php';
include '../config/session.php';
isCustomer();
;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Purchased Products - Velvet Crumbs</title>
    <link rel="stylesheet" href="./cs/product.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

</head>

<body>
    <header>
        <div class="brand"><img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span></div>
        <div>
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
    <div class="search-box">
        <input type="text" placeholder="Search for the items" />
    </div>
    <?php
    // Algorithm module: personalized recommendations
    include_once __DIR__ . "/algorithm.php";
    $recommended = [];
    if (isset($_SESSION['uid'])) {
        $recommended = get_recommended_products($db, (int) $_SESSION['uid'], 6);
    }
    ?>

    <?php if (!empty($recommended)): ?>
        <section class="reco-section">
            <div class="reco-head topic">
                <h2 class="reco-title">Recommended for you</h2>
                <span class="reco-pill">Personalized</span>
            </div>
            <div class="product-container reco-grid">
            <?php foreach ($recommended as $row): ?>
                <div class="product-card-wrapper">
                    <div class="product-card reco-card">
                        <div style="position: relative;">
                            <span class="badge"><?php echo htmlspecialchars($row['category']); ?></span>
                            <span class="reco-badge">For you</span>
                            <img src="../images/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                        </div>
                        <div class="product-info">
                            <div>
                                <h3><?php echo htmlspecialchars($row['p_name']); ?></h3>
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px;"
                                    title="<?php echo htmlspecialchars($row['description'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($row['description'] ?? ''); ?>
                                </p>
                            </div>
                            <div class="product-footer">
                                <span class="price">Rs <?php echo htmlspecialchars($row['price']); ?></span>
                                <div>
                                    <button class="view-btn"
                                        onclick="window.location.href='productdetail.php?pid=<?php echo (int) $row['pid']; ?>'">View
                                        Detail</button>
                                    <a class="add-btn"
                                        href="add_to_cart.php?pid=<?php echo (int) $row['pid']; ?>&uid=<?php echo (int) $_SESSION['uid']; ?>">Add
                                        to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <h2 class="topic">Most Purchased</h2>
    <div class="product-container">

        <?php
        $sql = "SELECT * FROM products";
        $result = mysqli_query($db, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="product-card-wrapper">
                    <div class="product-card">
                        <div style="position: relative;">
                            <span class="badge"><?php echo $row['category']; ?></span>
                            <img src="../images/<?php echo $row['image']; ?>" alt="Product Image">
                        </div>
                        <div class="product-info">
                            <div>
                                <h3><?php echo $row['p_name']; ?></h3>
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px;"
                                    title="<?php echo htmlspecialchars($row['description']); ?>">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </p>
                            </div>
                            <div class="product-footer">
                                <span class="price">Rs <?php echo $row['price']; ?></span>
                                <div>
                                    <button class="view-btn"
                                        onclick="window.location.href='productdetail.php?pid=<?php echo $row['pid']; ?>'">View
                                        Detail</button>
                                    <a class="add-btn"
                                        href="add_to_cart.php?pid=<?php echo $row['pid']; ?>&uid=<?php echo $_SESSION['uid']; ?>">Add
                                        to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
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
                    <li><a href="product.php">Products</a></li>
                    <li><a href="../about.php">About us</a></li>
                    <li><a href="../terms.php">Terms</a></li>
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

</body>

</html>