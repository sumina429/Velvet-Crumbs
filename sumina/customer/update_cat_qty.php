<?php

if (isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    change_cart_qty($cart_id, $quantity);
}

function change_cart_qty($cart_id, $quantity) {
    include '../config/db.php';
    $sql = "UPDATE cart SET quantity = $quantity WHERE cart_id = $cart_id";
    $result = mysqli_query($db, $sql);
    return $result;
}

?>