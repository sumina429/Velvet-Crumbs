<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="./register.css">
</head>

<body>

    <div class="register-card">
        <h2>Create Account</h2>

        <form action="user_process.php" method="GET">
            <!-- Hidden role field -->
            <input type="hidden" name="role" id="role" value="customer">

            <label for="fname">Full Name</label>
            <input type="text" id="fname" name="fullname">

            <label for="uname">Username</label>
            <input type="text" id="uname" name="username">

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email">

            <label for="pwd">Password</label>
            <input type="password" id="pwd" name="password">

            <label for="cpwd">Confirm Password</label>
            <input type="password" id="cpwd" name="confirm_password">

            <label class="checkbox" for="agree">
                <input type="checkbox" name="agree" id="agree" required>
                <span>I agree to the <a href="terms.php">Terms & Conditions</a></span>
            </label>

            <button type="submit" name="register" class="register-btn">Register</button>
        </form>

        <div class="login-text">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

</body>

</html>