<?php

include '../config/db.php';

if(isset($_GET['pid']) && isset($_GET['uid'])) {
    $pid = $_GET['pid'];
    $uid = $_GET['uid'];
    $sql = "INSERT INTO cart (pid, uid) VALUES ($pid, $uid)";
    $result = mysqli_query($db, $sql);
    // After adding, go right back to previous page
    if($result) {
        echo "Product added to cart successfully";
    } else {
        echo "Error adding product to cart";
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        // If referer is not available, go to cart
        header("Location: cart.php");
        exit;
    }
}

?>