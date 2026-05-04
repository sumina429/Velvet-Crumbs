<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Gather POST fields
    $product_name = trim($_POST['product_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $stock_quantity = ($_POST['stock_quantity'] !== '') ? intval($_POST['stock_quantity']) : null;
    $description = trim($_POST['description'] ?? '');

    // ✅ Check required fields
    if (empty($product_name) || empty($category) || $price <= 0) {
        echo "<script>
                alert('Error: Please fill all required fields correctly.');
                window.history.back();
              </script>";
        exit;
    }

    // ✅ Image upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>
                alert('Error: Image upload failed.');
                window.history.back();
              </script>";
        exit;
    }

    $file = $_FILES['image'];
    $imageFileName = basename($file['name']);
    $uploadFolder = "../images/";
    $targetFilePath = $uploadFolder . $imageFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        echo "<script>
                alert('Error: Unable to upload image. Check folder permissions.');
                window.history.back();
              </script>";
        exit;
    }

    // ✅ Insert into database
    $stmt = $db->prepare(
        "INSERT INTO products (p_name, category, price, stock, image, description)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        echo "<script>
                alert('Database error: Unable to prepare statement.');
                window.history.back();
              </script>";
        exit;
    }

    $stmt->bind_param(
        "ssiiss",
        $product_name,
        $category,
        $price,
        $stock_quantity,
        $imageFileName,
        $description
    );

    if ($stmt->execute()) {
        echo "<script>
                alert('Product added successfully!');
                window.location.href = 'product.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Database error: Failed to insert product.');
                window.history.back();
              </script>";
        exit;
    }
}
?>
