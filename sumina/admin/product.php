<?php

include '../config/db.php';
include '../config/session.php';
isAdmin();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="./csss/product.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

    <header>
        <div class="brand"><img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span></div>
        <div class="header-actions">
            <a class="user-pill">👤 <?php echo $_SESSION['username']; ?></a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </header>

    <section class="banner">
        <h2>Vendor Dashboard</h2>
        <p>Velvet crumbs</p>
    </section>

    <nav class="tabs">
        <a href="overview.php">Overview</a>
        <a href="#" class="active">Products</a>
        <a href="orders.php">Orders</a>
        <a href="profile.php">Business Profile</a>
        <a href="report.php">Report</a>
    </nav>

    <main class="container">

        <h3 class="section-title">Manage Products</h3>

        <form action="add_process.php" enctype="multipart/form-data" method="post" class="product-form"
            style="display: flex; align-items: flex-start; gap: 24px;">
            <div style="flex: 0 0 200px;">
                <div class="image-upload-container">
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="Preview">
                        <input type="file" name="image" id="img" accept="image/*">
                    </div>
                </div>
            </div>
            <div style="flex:1; display: flex; flex-direction: column; gap:10px;">
                <input type="text" name="product_name" placeholder="Product Name">
                <input type="text" name="category" placeholder="Category">
                <input type="number" name="price" placeholder="Price">
                <input type="number" name="stock_quantity" placeholder="Stock Quantity">
                <textarea name="description" placeholder="Description"></textarea>
                <button type="submit" class="add-btn" style="align-self: flex-end;">Add Product</button>
            </div>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php


                    $sql = "SELECT * FROM products";
                    $result = $db->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['p_name']) . "</td>";
                            echo "<td>Rs. " . htmlspecialchars($row['price']) . "</td>";
                            echo "<td>" . ($row['stock'] !== null ? htmlspecialchars($row['stock']) : 'N/A') . "</td>";
                            echo "<td>
                                <a href='edit_page.php?pid=" . htmlspecialchars($row['pid']) . "&action=edit' class='edit'>Edit</a>
                                <a href='edit_data.php?pid=" . htmlspecialchars($row['pid']) . "&action=delete' class='delete'>Delete</a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>No products found.</td></tr>";
                    }

                    $db->close();
                    ?>
                </tbody>
            </table>
        </div>

    </main>

    <script>
        const imageInput = document.getElementById('img');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>

</html>