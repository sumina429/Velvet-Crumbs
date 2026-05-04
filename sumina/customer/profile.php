<?php

include '../config/db.php';
include '../config/session.php';
isCustomer();

$uid = $_SESSION['uid'];
$sql = "SELECT * FROM users WHERE uid = $uid";
$result = mysqli_query($db, $sql);
$users = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile - Velvet Crumbs</title>
    <link rel="stylesheet" href="./cs/profile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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

    
    <div class="banner" style="display: flex; align-items: start; gap: 18px;">
        <?php

        $uname_display = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $uid = isset($users['uid']) ? $users['uid'] : "";
        $username = isset($users['username']) ? $users['username'] : "";
        $email = isset($users['email']) ? $users['email'] : "";
        $phone = isset($users['phone']) ? $users['phone'] : "";
        $address = isset($users['address']) ? $users['address'] : "";
        $created_at = isset($users['created_at']) ? $users['created_at'] : "";
        $dob = isset($users['dob']) ? $users['dob'] : "";
        // Add more columns if you know them, following the same pattern


        if (strlen($uname_display) >= 2) {
            $uname_initials = strtoupper(substr($uname_display, 0, 1)) . strtolower(substr($uname_display, 1, 1));
        } elseif (strlen($uname_display) == 1) {
            $uname_initials = strtoupper(substr($uname_display, 0, 1));
        } else {
            $uname_initials = "";
        }
        ?>
        <div 
            class="w-16 h-16 rounded-full bg-white flex items-center justify-center text-[1.5rem] font-bold text-[#e645a2] border-2 border-[#ddd] select-none" 
            aria-label="Profile Icon"
        >
            <?php echo $uname_initials; ?>
        </div>
        <div>
            <h1 style="margin-bottom: 0;">Customer Dashboard</h1>
            <?php if (!empty($uname_display)): ?>
                <p><?php echo htmlspecialchars($uname_display); ?></p>
            <?php endif; ?>
            <?php if (!empty($email)): ?>
                <p><?php echo htmlspecialchars($email); ?></p>
            <?php endif; ?>
            <?php if (!empty($phone)): ?>
                <p><?php echo htmlspecialchars($phone); ?></p>
            <?php endif; ?>
            <?php if (!empty($address)): ?>
                <p><?php echo htmlspecialchars($address); ?></p>
            <?php endif; ?>
            <?php if (!empty($created_at)): ?>
                <p><?php echo htmlspecialchars($dob); ?></p>
            <?php endif; ?>
        <a href='manage_profile.php' class="bg-white text-[#ec4899] rounded-lg px-4 py-2 text-[15px] inline-flex items-center shadow-sm mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#ec4899" class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 3v1.016a7.496 7.496 0 00-3.024 1.252l-.72-.72a.75.75 0 00-1.06 0l-1.061 1.06a.75.75 0 000 1.061l.72.721A7.497 7.497 0 004.016 11.25H3a.75.75 0 000 1.5h1.016a7.5 7.5 0 001.252 3.024l-.72.72a.75.75 0 000 1.061l1.061 1.06a.75.75 0 001.06 0l.721-.72a7.497 7.497 0 003.024 1.252V21a.75.75 0 001.5 0v-1.016a7.497 7.497 0 003.024-1.252l.721.72a.75.75 0 001.06 0l1.061-1.06a.75.75 0 000-1.061l-.72-.72a7.5 7.5 0 001.252-3.024H21a.75.75 0 000-1.5h-1.016a7.497 7.497 0 00-1.252-3.024l.72-.721a.75.75 0 000-1.061l-1.06-1.06a.75.75 0 00-1.061 0l-.72.72A7.497 7.497 0 0012.75 4.016V3a.75.75 0 00-1.5 0zm.75 6a3.25 3.25 0 110 6.5 3.25 3.25 0 010-6.5z"/>
            </svg>
            <span>Manage</span>
        </a>
        </div>
    </div>


    <!-- <div class="dashboard-grid">
        <a href="./checkout.php" class="dash-card">
            <div class="icon-box">🕒</div>
            <span>Purchase History</span>
        </a>
        <a href="./checkout.php" class="dash-card">
            <div class="icon-box">📋</div>
            <span>Order Detail</span>
        </a>
        <a href="../customer/tracking.php" class="dash-card">
            <div class="icon-box">🚚</div>
            <span>Tracking</span>
        </a>
        <a href="./checkout.php" class="dash-card">
            <div class="icon-box">💳</div>
            <span>Payment Detail</span>
        </a>
        <a href="./checkout.php" class="dash-card">
            <div class="icon-box">⚙️</div>
            <span>Setting</span>
        </a>
    </div> -->
    
    <div class="max-w-6xl mx-auto px-4 pb-20">
        <h2 class="text-3xl font-semibold text-[#b34d85] my-6">Order Management</h2>
    
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
            <?php
            // Fetch orders for the logged-in customer
            $uid = $_SESSION['uid'];
            $sql_orders = "SELECT * FROM orders WHERE uid = $uid ORDER BY created_at DESC";
            $result_orders = mysqli_query($db, $sql_orders);

            // For status styling
            $status_colors = [
                "pending" => ["#fffbe7", "#b26c00"],
                "processing" => ["#e6f4ff", "#026aa7"],
                "shipped" => ["#e5fbe6", "#199f3a"],
                "delivered" => ["#e6fffb", "#08979c"],
                "cancelled" => ["#ffebee", "#d32f2f"]
            ];
            ?>
            <table class="min-w-full text-left border-separate border-spacing-0 bg-white rounded-t-xl overflow-hidden order-table">
                <thead class="bg-[#b34d85] text-white text-sm font-semibold text-center">
                    <tr>
                        <th class="py-4 px-6 border-y border-white border-x first:rounded-tl-xl last:rounded-tr-xl border-l border-r">Order ID</th>
                        <th class="py-4 px-6 border-y border-white border-x border-l border-r">Total</th>
                        <th class="py-4 px-6 border-y border-white border-x border-l border-r">Ordered At</th>
                        <th colspan='2' class="py-4 px-6 border-y border-white border-x border-l border-r">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_orders && mysqli_num_rows($result_orders) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result_orders)):
                            $oid = $row['oid'];
                            $total = number_format($row['total'], 2);
                            $ordered_at = date("Y-m-d H:i", strtotime($row['created_at']));
                            $status = strtolower($row['status']);
                            $color_bg = $status_colors[$status][0] ?? "#fffbe7";
                            $color_fg = $status_colors[$status][1] ?? "#b26c00";
                            $inline_style = "background: $color_bg; color: $color_fg; padding: 6px 18px; border-radius:14px; display:inline-block; font-weight: 600; font-size: 13px; min-width:100px; text-align:center;";
                        ?>
                        <tr class="even:bg-[#faf8fc] odd:bg-white">
                            <td class="w-28 text-gray-800 border-l border-white border-r border-b border-b-white text-center"><?php echo htmlspecialchars($oid); ?></td>
                            <td class="py-2 border-l border-white border-r border-b border-b-white text-center">Rs. <?php echo $total; ?></td>
                            <td class="border-l border-white border-r border-b border-b-white text-center"><?php echo htmlspecialchars($ordered_at); ?></td>
                            <td class="border-l border-white border-r border-b border-b-white text-center">
                                <span style="<?php echo $inline_style; ?>"><?php echo strtoupper($status); ?></span>
                            </td>
                            <td class="w-40 border-l border-white border-r border-b border-b-white text-center">
                                <a href="tracking.php?oid=<?= $oid ?>">🚚 ... <span class="text-sm text-gray-400">Track Order</span></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-400 border-b border-white">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>