<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | User Management</title>
    <link rel="stylesheet" href="./login_old.css">
</head>

<body>
    <main class="container">
        <h1 class="page-title">REGISTER</h1>

        <form action="user_process.php" method="GET" name="user_form">
            <div class="field-group">
                <label for="fname">Full Name</label>
                <input type="text" id="fname" name="fullname">
            </div>

            <div class="field-group">
                <label for="uname">Username</label>
                <input type="text" id="uname" name="username">
            </div>

            <div class="field-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="field-group">
                <label for="pwd">Password</label>
                <input type="password" id="pwd" name="password">
            </div>

            <div class="field-group">
                <label for="cpwd">Confirm Password</label>
                <input type="password" id="cpwd" name="confirm_password">
            </div>
            <div class="field-group">
                <input type="checkbox" id="agree" name="agree">
                <label for="agree">I agree to the <a href="agree.php" title="Terms and Conditions">Terms of
                        Service</a></label>
            </div>
            <button type="submit" name="register" class="btn">Register</button>
        </form>
        <div class="btn-group">
            <hr class="note">
            Already have an account? <a href="login.php" class="text-link">Login Now</a>
            </span><br><br>
        </div>
    </main>
</body>

</html>