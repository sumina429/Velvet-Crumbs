<?php
include '../config/db.php';
include '../config/session.php';
isAdmin();

// Fetch only orders, showing oid, total, created_at (as ordered at), and status.
$query = "SELECT oid, total, created_at, status FROM orders ORDER BY oid DESC";
$result = mysqli_query($db, $query);

$status_classes = [
    "pending" => "status-pending",
    "processing" => "status-processing",
    "shipped" => "status-shipped",
    "delivered" => "status-delivered",
    "cancelled" => "status-cancelled"
];

// Define distinct background and text colors for each status
$status_colors = [
    "pending" => ["#fffbe7", "#b26c00"],        // light yellow bg, dark amber text
    "processing" => ["#e6f4ff", "#026aa7"],     // very light blue bg, blue text
    "shipped" => ["#e5fbe6", "#199f3a"],        // very light green bg, dark green text
    "delivered" => ["#e6fffb", "#08979c"],      // light aqua bg, teal text
    "cancelled" => ["#ffebee", "#d32f2f"]       // light red bg, dark red text
];

$all_statuses = ["pending", "processing", "shipped", "delivered", "cancelled"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order Management - Velvet Crumbs</title>
    <link rel="stylesheet" href="./csss/order.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    
</head>

<body>
    <header>
        <div class="brand"><img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span></div>
        <div class="header-actions">
            <a class="user-pill">👤 <?php echo $_SESSION['username']; ?></a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </header>

    <div class="dashboard-header">
        <h2 style="margin:0; font-size: 28px;">Admin Dashboard</h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Velvet Crumbs</p>
    </div>

    <nav class="tabs">
        <a href="overview.php">Overview</a>
        <a href="product.php">Products</a>
        <a href="#" class="active">Orders</a>
        <a href="profile.php">Business Profile</a>
        <a href="report.php">Report</a>
    </nav>

    <main class="max-w-6xl mx-auto px-4 pb-20">
        <h2 class="text-[22px] font-semibold text-[#b34d85] mb-6">Order Management</h2>

        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total</th>
                        <th>Ordered At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)):
                            $oid = $row['oid'];
                            $total = number_format($row['total'], 2);
                            $ordered_at = date("Y-m-d H:i", strtotime($row['created_at']));
                            $status = strtolower($row['status']);
                            $status_class = $status_classes[$status] ?? "status-pending";
                            $color_bg = $status_colors[$status][0] ?? "#fffbe7";
                            $color_fg = $status_colors[$status][1] ?? "#b26c00";
                            $inline_style = "background: $color_bg; color: $color_fg;";
                        ?>
                        <tr>
                            <td class="font-bold text-gray-800"><?php echo htmlspecialchars($oid); ?></td>
                            <td>Rs. <?php echo $total; ?></td>
                            <td><?php echo htmlspecialchars($ordered_at); ?></td>
                            <td>
                                <span class="status-select-wrapper">
                                    <select 
                                        id="status-<?php echo $oid; ?>" 
                                        class="status-select <?php echo $status_class; ?>" 
                                        style="<?php echo $inline_style; ?>"
                                        onchange="location.href='update_status.php?oid=<?php echo $oid; ?>&status='+this.value;">
                                        <!-- Show the status from database as the first option -->
                                        <option value="<?php echo htmlspecialchars($status); ?>" selected>
                                            <?php echo ucfirst($status); ?>
                                        </option>
                                        <?php foreach($all_statuses as $st): ?>
                                            <?php if ($st !== $status): ?>
                                                <option value="<?php echo $st; ?>">
                                                    <?php echo ucfirst($st); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach ?>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:#888;">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
        </div>
    </main>
</body>

</html>