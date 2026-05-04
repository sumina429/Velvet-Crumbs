<?php
include '../config/db.php';
include '../config/session.php';
isAdmin();



$sql = "select sum(pid) as total_products from products;";
$result = mysqli_query($db, $sql);
$data = mysqli_fetch_assoc($result);
$total_products = $data['total_products'];

$sql = "select sum(p.stock) as total_stock from orders o join order_items oi on o.oid = oi.oid join products p on p.pid = oi.pid";
$result = mysqli_query($db, $sql);
$data = mysqli_fetch_assoc($result);
$total_stock = $data['total_stock'];

$sql_delivered = "SELECT oid, SUM(total) as revenue from orders where status = 'delivered';";
$result_delivered = mysqli_query($db, $sql_delivered);
$data_delivered = mysqli_fetch_assoc($result_delivered);

$revenue = $data_delivered['revenue'];
$oid_delevered = $data_delivered['oid'];

// Get total items sold (across all delivered orders)
$sql_delivered = "SELECT SUM(oi.quantity) as sold_items 
                  FROM order_items oi 
                  JOIN orders o ON oi.oid = o.oid 
                  WHERE o.status = 'delivered';";
$result_delivered = mysqli_query($db, $sql_delivered);
$data_delivered = mysqli_fetch_assoc($result_delivered);

$sold_items = $data_delivered['sold_items'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Velvet Crumbs</title>
    <link rel="stylesheet" href="./csss/overview.css">
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
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Velvet Crumbs Overview</p>
    </div>

    <nav class="tabs">
        <a href="overview.php" class="active">Overview</a>
        <a href="product.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="profile.php">Business Profile</a>
        <a href="report.php">Report</a>
    </nav>

    <main class="max-w-6xl mx-auto px-4 pb-20">
        <h2 class="text-[22px] font-semibold text-[#b34d85] mb-8">Business Overview</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm mb-2">Total Revenue</p>
                        <p class="text-[#40c057] text-lg font-medium">
                            Rs.<?php echo $revenue; ?>
                        </p>
                    </div>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#40c057" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                </div>
                <p class="text-gray-400 text-[13px]">From delivered orders</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm mb-2">Total Products</p>
                        <p class="text-[#a855f7] text-3xl font-bold">
                            <?php echo $total_products; ?>
                        </p>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <p class="text-gray-400 text-[13px]">
                    All active
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm mb-2">Items Sold</p>
                        <p class="text-[#fca311] text-3xl font-bold">
                            <?php echo $sold_items; ?>
                        </p>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fca311" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                        </path>
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M3.29 7 12 12l8.71-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                </div>
                <p class="text-gray-400 text-[13px]">From all time delivered</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


        </div>
    </main>

</body>

</html>