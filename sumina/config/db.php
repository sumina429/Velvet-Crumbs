<?php
$db = new mysqli('localhost', 'root', '', 'velvet_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>