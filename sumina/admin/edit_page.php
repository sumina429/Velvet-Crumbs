<?php

include "../config/db.php";
include "../config/session.php";
isAdmin();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="./csss/product.css">
    <style>
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }
        .save-btn {
            background: #ec4899;
            border: none;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }
        .cancel-btn {
            background: #eee;
            border: none;
            color: #444;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .image-upload-container .image-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .image-upload-container img {
            display: block;
            max-width: 120px;
            max-height: 120px;
            margin-bottom: 8px;
        }
    </style>
</head>

<?php
$pid = intval($_GET['pid'] ?? 0);
?>

<body>
<?php
// Fetch data from db for the product with $pid
$product = [
    'p_name' => '',
    'category' => '',
    'price' => '',
    'stock' => '',
    'description' => '',
    'image' => ''
];

// Fetch product data to prefill the form and get old image name
$stmt = $db->prepare("SELECT * FROM products WHERE pid = ?");
$stmt->bind_param('i', $pid);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $product = $row;
}
$stmt->close();

$old_image_filename = $product['image'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_name = $_POST['p_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $description = $_POST['description'] ?? '';
    $updateImage = false;
    $image_filename = $old_image_filename;

    // Handle file upload if a new file was provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../images/";
        $image_filename = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_filename;

        // Delete old image if it exists and isn't empty and is not the same as the new file
        if (!empty($old_image_filename) && file_exists($target_dir . $old_image_filename) && $image_filename !== $old_image_filename) {
            @unlink($target_dir . $old_image_filename);
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $updateImage = true;
        } else {
            echo "<div style='color:red'>Failed to upload new image.</div>";
        }
    }

    if ($updateImage) {
        $stmt = $db->prepare("UPDATE products SET p_name=?, category=?, price=?, stock=?, description=?, image=? WHERE pid=?");
        $stmt->bind_param("ssdissi", $p_name, $category, $price, $stock, $description, $image_filename, $pid);
    } else {
        $stmt = $db->prepare("UPDATE products SET p_name=?, category=?, price=?, stock=?, description=? WHERE pid=?");
        $stmt->bind_param("ssdisi", $p_name, $category, $price, $stock, $description, $pid);
    }

    if ($stmt->execute()) {
        // Redirect to product.php after update
        header("Location: product.php");
        exit;
    } else {
        echo "<div style='color:red'>Update failed: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
}

// Refetch updated product data to display in the form
$stmt = $db->prepare("SELECT * FROM products WHERE pid = ?");
$stmt->bind_param('i', $pid);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $product = $row;
}
$stmt->close();
?>

<div class="container" style="margin-top:48px;">
    <h2 style="margin-bottom:24px;">Edit Product</h2>
    <form action="" method="post" enctype="multipart/form-data" class="product-form"
          style="display: flex; align-items: flex-start; gap: 24px;">
        <div style="flex: 0 0 200px;">
            <div class="image-upload-container">
                <div class="image-preview" id="imagePreview">
                    <?php if (!empty($product['image']) && file_exists("../images/" . $product['image'])): ?>
                        <img id="previewImg" src="../images/<?php echo htmlspecialchars($product['image']); ?>" alt="Preview">
                    <?php else: ?>
                        <img id="previewImg" src="" alt="Preview" style="display:none;">
                    <?php endif; ?>
                    <input type="file" name="image" id="img" accept="image/*" onchange="previewFile(this)">
                </div>
            </div>
        </div>
        <div style="flex:1; display: flex; flex-direction: column; gap:10px;">
            <input type="text" name="p_name" placeholder="Product Name" value="<?php echo htmlspecialchars($product['p_name']); ?>">
            <input type="text" name="category" placeholder="Category" value="<?php echo htmlspecialchars($product['category']); ?>">
            <input type="number" name="price" placeholder="Price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>">
            <input type="number" name="stock" placeholder="Stock Quantity" min="0" value="<?php echo htmlspecialchars($product['stock']); ?>">
            <textarea name="description" placeholder="Description"><?php echo htmlspecialchars($product['description']); ?></textarea>
            <div class="btn-group">
                <button type="submit" class="save-btn">Update Product</button>
                <a href="javascript:window.history.back();" class="cancel-btn">Cancel</a>
            </div>
        </div>
    </form>
</div>
<script>
function previewFile(input) {
    var file = input.files && input.files[0];
    var previewImg = document.getElementById('previewImg');
    if (file) {
        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = 'block';
    } else {
        previewImg.src = '';
        previewImg.style.display = 'none';
    }
}
</script>
</body>

</html>