<?php
include '../config/db.php';
include '../config/session.php';
if(isset($_GET['cart_id']) && isset($_GET['pid'])) {
    $cid = $_GET['cart_id'];
    $uid = $_SESSION['uid'];
    $cart_id = $_GET['cart_id'];
    $sql = "DELETE FROM cart WHERE cart_id = $cart_id AND uid = $uid";
    $result = mysqli_query($db, $sql);
    if($result) {
        header("Location: cart.php");
    }

}
?>