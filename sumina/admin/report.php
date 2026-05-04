<?php
include '../config/db.php'; 
include '../config/session.php';
isAdmin();

// Database Connection Fallback
if (!isset($conn)) {
    if (isset($con)) { $conn = $con; } 
    elseif (isset($mysqli)) { $conn = $mysqli; } 
    else { $conn = mysqli_connect("localhost", "root", "", "velvet_db"); }
}

$report_rows = [];
$total_revenue = 0;
$total_items = 0;
$show_report = false;

if (isset($_GET['generate_report'])) {
    $show_report = true;
    $type = $_GET['type'];
    $selected_date = $_GET['selected_date'];

    if ($type == 'Daily') {
        $start = $selected_date . " 00:00:00";
        $end   = $selected_date . " 23:59:59";
    } elseif ($type == 'Weekly') {
        $start = date('Y-m-d 00:00:00', strtotime($selected_date . ' -6 days'));
        $end   = $selected_date . " 23:59:59";
    } else { 
        $start = date('Y-m-01 00:00:00', strtotime($selected_date));
        $end   = date('Y-m-t 23:59:59', strtotime($selected_date));
    }

    $sql = "SELECT o.*, SUM(oi.quantity) as item_count 
            FROM orders o 
            LEFT JOIN order_items oi ON o.oid = oi.oid 
            WHERE o.created_at BETWEEN '$start' AND '$end' 
            GROUP BY o.oid 
            ORDER BY o.created_at DESC";
            
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $report_rows[] = $row;
            $total_revenue += $row['total'];
            $total_items += $row['item_count'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Velvet Crumbs</title>
    <link rel="stylesheet" href="./csss/profile.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Custom styles to match the report table with the "section-card" look */
        .section-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            border-left: 4px solid #d8439b;
            padding-left: 1rem;
        }
    </style>
</head>

<body class="bg-gray-50">

    <header>
        <div class="brand"><img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span></div>
        <div class="header-actions">
            <a class="user-pill">👤 <?php echo $_SESSION['username']; ?></a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </header>

    <div class="hero-banner">
        <h1>Admin Dashboard</h1>
        <p>Velvet Crumbs</p>
    </div>

    <nav class="tabs-container">
        <a href="overview.php" class="tab-link">Overview</a>
        <a href="product.php" class="tab-link">Products</a>
        <a href="orders.php" class="tab-link">Orders</a>
        <a href="profile.php" class="tab-link">Business Profile</a>
        <a href="report.php" class="tab-link active">Report</a>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">

        <div class="section-card">
            <h2 class="section-title">Sales & Transaction Reports</h2>
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Report Type</label>
                    <select name="type" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                        <option value="Daily" <?php if(isset($_GET['type']) && $_GET['type'] == 'Daily') echo 'selected'; ?>>Daily</option>
                        <option value="Weekly" <?php if(isset($_GET['type']) && $_GET['type'] == 'Weekly') echo 'selected'; ?>>Weekly</option>
                        <option value="Monthly" <?php if(isset($_GET['type']) && $_GET['type'] == 'Monthly') echo 'selected'; ?>>Monthly</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Select Date</label>
                    <input type="date" name="selected_date" value="<?php echo $_GET['selected_date'] ?? ''; ?>" required
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                </div>
                <button type="submit" name="generate_report"
                    class="bg-gradient-to-r from-[#d8439b] to-[#a044ff] text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:opacity-90 transition-all">
                    Generate Report
                </button>
            </form>
        </div>

        <?php if ($show_report): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="section-card flex flex-col items-center justify-center border-b-4 border-pink-500">
                    <span class="text-xs font-bold text-gray-400 uppercase">Total Products Sold</span>
                    <span class="text-3xl font-black text-gray-800"><?php echo number_format($total_items); ?> Units</span>
                </div>
                <div class="section-card flex flex-col items-center justify-center border-b-4 border-purple-500">
                    <span class="text-xs font-bold text-gray-400 uppercase">Total Revenue</span>
                    <span class="text-3xl font-black text-gray-800">Rs. <?php echo number_format($total_revenue, 2); ?></span>
                </div>
            </div>

            <div class="section-card overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-4">Order ID</th>
                            <th class="pb-4">Date & Time</th>
                            <th class="pb-4">Items</th>
                            <th class="pb-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($report_rows)): ?>
                            <tr><td colspan="4" class="py-8 text-center text-gray-400 italic">No records found for this period.</td></tr>
                        <?php else: ?>
                            <?php foreach ($report_rows as $row): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 font-bold text-purple-600">#<?php echo $row['oid']; ?></td>
                                    <td class="py-4 text-sm text-gray-600"><?php echo date('M d, Y - h:i A', strtotime($row['created_at'])); ?></td>
                                    <td class="py-4 text-sm text-gray-600"><?php echo $row['item_count'] ?? 0; ?> Units</td>
                                    <td class="py-4 text-right font-bold text-gray-800">Rs. <?php echo number_format($row['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="section-card text-center py-12">
                <p class="text-gray-400 italic">Please select filters above to generate a business report.</p>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>