<?php
include '../config/db.php';

// Get order ID and new status from query parameters
$oid = isset($_GET['oid']) ? intval($_GET['oid']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

// Only allow if all info present and status is valid
if ($oid > 0 && in_array($status, $allowed_statuses)) {
    // Update status in the database (always use prepared statements for security)
    $stmt = mysqli_prepare($db, "UPDATE orders SET status=? WHERE oid=?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $status, $oid);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            // Redirect back to orders.php after successful update
            header("Location: orders.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
    // If update or preparation failed, fall through to error
}

// Error handling: display error and link back to orders page
$message = "Failed to update order status. Please make sure the order ID and status are valid.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Order Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gray-50">
    <div class="bg-white px-10 py-8 rounded-lg shadow-lg mt-10 text-center max-w-lg">
        <div class="text-red-600 font-semibold text-lg mb-4">Error</div>
        <div class="mb-4"><?php echo $message; ?></div>
        <a href="orders.php" class="mt-4 inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-bold text-sm shadow transition">Back to Orders</a>
    </div>
</body>
</html>