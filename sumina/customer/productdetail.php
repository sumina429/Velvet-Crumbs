<?php
include "../config/db.php";
include "../config/session.php";

$sql = "SELECT * FROM products WHERE pid={$_GET['pid']}";
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($row['p_name'] ?? 'Product'); ?> - Details</title>
  <link rel="stylesheet" href="./cs/productdetail.css">
</head>

<body>


  <div class="details-view-container">
    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] === "customer" && isset($_SESSION['uid'])) {
      $back_link = "product.php";
    } else {
      $back_link = "../";
    }
    ?>
    <a href="<?php echo $back_link; ?>" class="back-link">← Back to Items</a>

    <div class="details-card">
      <div class="details-header">
        <h2 id="page-title" style="margin:0;">Product Details</h2>
        <a href="<?php echo $back_link; ?>" style="text-decoration:none; font-size:24px; color:#333;">×</a>
      </div>

      <div class="details-content-grid">
        <div class="details-img-box">
          <img id="product-img" src="../images/<?php echo $row['image']; ?>" alt="Product">
        </div>

        <div class="details-info-side">
          <div class="info-gray-box">
            <h1 id="product-name"><?php echo $row['p_name']; ?></h1>
            <p id="product-desc"><?php echo $row['description']; ?></p>
          </div>

          <div class="price-pill" id="product-price">Rs. <?php echo $row['price']; ?></div>

          <?php
          if (isset($_SESSION['role']) && $_SESSION['role'] === "customer" && isset($_SESSION['uid'])) {
            ?>
            <a href="add_to_cart.php?pid=<?php echo $row['pid']; ?>&uid=<?php echo $_SESSION['uid']; ?>"
              class="add-cart-btn">
              🛒 Add to Cart
            </a>
            <?php
          } else {
            ?>
            <a href="../login.php" class="add-cart-btn">
              Log In to Add to cart
            </a>
            <?php
          }
          ?>
        </div>
      </div>

    </div>
  </div>

</body>

</html>