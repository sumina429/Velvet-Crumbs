<?php
include '../config/db.php';

if ($_GET['action'] == 'update') {
    if (isset($_GET['pid'])) {
        $edit_pid = $_GET['pid'];
        
    }

} else if ($_GET['action'] == 'delete') {
    // Get product ID from URL
    $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

    if ($pid > 0) {
        // First, get the image filename from the database
        $stmt = $db->prepare("SELECT image FROM products WHERE pid = ?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $imageFileName = $row['image'];

            // Delete the image file from images folder
            if (!empty($imageFileName)) {
                $imagePath = "../images/" . basename($imageFileName);

                // Check if file exists and delete it
                if (file_exists($imagePath)) {
                    if (unlink($imagePath)) {
                        // Image file deleted successfully
                    } else {
                        // Could not delete image file, but continue with database deletion
                        error_log("Warning: Could not delete image file: " . $imagePath);
                    }
                }
            }

            // Delete the product from database
            $deleteStmt = $db->prepare("DELETE FROM products WHERE pid = ?");
            $deleteStmt->bind_param("i", $pid);

            if ($deleteStmt->execute()) {
                // Redirect back to product page with success message
                header("Location: product.php?deleted=1");
                exit;
            } else {
                echo "Error deleting product: " . $deleteStmt->error;
            }

            $deleteStmt->close();
        } else {
            echo "Product not found.";
        }

        $stmt->close();
    } else {
        echo "Invalid product ID.";
    }

    $db->close();
}

?>