<?php
include '../config/db.php';
include '../config/session.php';
isAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Velvet Crumbs</title>
    <link rel="stylesheet" href="./csss/profile.css">
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

    <div class="hero-banner">
        <h1>Admin Dashboard</h1>
        <p>Velvet Crumbs </p>
    </div>

    <nav class="tabs-container">
        <a href="overview.php" class="tab-link">Overview</a>
        <a href="product.php" class="tab-link">Products</a>
        <a href="orders.php" class="tab-link">Orders</a>
        <a href="" class="tab-link active">Business Profile</a>
        <a href="report.php" class="tab-link">Report</a>
    </nav>

    <main class="max-w-6xl mx-auto px-4">

        <div class="section-card">
            <h2 class="section-title">Business Details</h2>
            <form class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Bakery Name</label>
                        <input type="text" placeholder="Velvet Crumbs"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Contact Number</label>
                        <input type="text" placeholder="+977-XXXXXXXXXX"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Business Mail</label>
                    <input type="text" placeholder="official@velvetcrumbs.com"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Business Branches</label>
                    <input type="text" placeholder="Bhaktapur, Jhamsikhel, Basantapur"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Business Main Branch</label>
                    <input type="text" placeholder="Kathmandu "
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-purple-400">
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-[#d8439b] to-[#a044ff] text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:opacity-90 transition-all">
                    Update Profile
                </button>
            </form>
        </div>

        </div>

    </main>

</body>

</html>