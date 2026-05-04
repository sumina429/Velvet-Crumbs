<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — Velvet Crumbs</title>
    <link rel="stylesheet" href="./register.css">
</head>

<body>

    <div class="register-card">
        <h2>Contact Us</h2>
        <p style="text-align: center; font-size: 14px; color: #666; margin-bottom: 20px;">Have a question? We'd love to hear from you!</p>

        <form action="contact_process.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #ddd; margin-bottom: 12px; font-size: 13px; resize: vertical;" required></textarea>

            <button type="submit" name="contact" class="register-btn">Send Message</button>
        </form>

        <div class="login-text">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>

</body>

</html></content>
<parameter name="filePath">c:\OS\htdocs\sumina\contact.php