<?php
include 'config/db.php';
include 'config/session.php';

if (isset($_POST['contact'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    // For now, just display a thank you message
    // In a real app, you might send an email or store in DB

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Message Sent — Velvet Crumbs</title>
    <link rel='stylesheet' href='./register.css'>
</head>
<body>
    <div class='register-card'>
        <h2>Thank You!</h2>
        <p style='text-align: center; font-size: 14px; color: #666; margin-bottom: 20px;'>Your message has been sent successfully. We'll get back to you soon!</p>
        <div class='login-text'>
            <a href='index.php'>← Back to Home</a>
        </div>
    </div>
</body>
</html>";
    exit();
}
?></content>
<parameter name="filePath">c:\OS\htdocs\sumina\contact_process.php