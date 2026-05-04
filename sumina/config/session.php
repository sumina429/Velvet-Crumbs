<?php

$bypass_session = 1;

session_start();

function isLoggedIn()
{
    if (!$_SESSION['username'])
        header("Location: ../login.php");
}

function isAdmin()
{
    if ($_SESSION['role'] != "admin")
        header("Location: ../login.php");
}
function isCustomer()
{
    if ($_SESSION['role'] != "customer")
        header("Location: ../login.php");
}

function login_to($username, $role, $uid)
{
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['uid'] = $uid;
}