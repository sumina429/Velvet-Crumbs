<?php
include 'config/session.php';
session_destroy();
header("Location: index.php");
exit();
?>