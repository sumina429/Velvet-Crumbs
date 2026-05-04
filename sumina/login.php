<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin</title>
    <link rel="stylesheet" href="./login.css">
    <style>
        .error-message {
            background: #ffe3e3;
            color: #d7263d;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 18px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #fd8888;
            font-size: 1.05em;
            animation: fadeout 1s linear 4s forwards;
        }
        @keyframes fadeout {
            to { opacity: 0; }
        }
    </style>
</head>

<body>
      <div class="register-card">
        <h2>Create Account</h2>

        <?php if (isset($_SESSION['login_message']) && !empty($_SESSION['login_message'])): ?>
            <div class="error-message" id="login-error">
                <?php echo htmlspecialchars($_SESSION['login_message']); ?>
            </div>
            <script>
                setTimeout(function() {
                    // Optionally fade out or remove the error message
                    var msg = document.getElementById('login-error');
                    if(msg) msg.style.display = 'none';
                }, 5000);
            </script>
            <?php
            // Destroy session after showing error
            session_destroy();
            ?>
        <?php endif; ?>

        <form action="user_process.php" method="GET">

            <label for="uname">Username / E-Mail</label>
            <input type="text" id="uname" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="pwd" name="password" required>

            <div class="checkbox">
                <input type="checkbox" id="rem" name="remember">
                <label for="rem">Remember login credentials</label>
            </div>

            <button type="submit" name="login" class="register-btn">Log In</button>
        </form>

        <div class="login-text">
            Already have an account? <a href="register.php" class="text-link">Register Now</a>
            <br>
            Become a <a href="admin_register.php" class="text-link">Vender</a> ?
        </div>
    </div>

</body>

</html>