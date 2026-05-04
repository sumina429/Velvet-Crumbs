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
        <h1 class="page-title">LOGIN</h1>
        <form action="user_process.php" method="GET" name="user_form">
            <div class="field-group">
                <label for="uname">Username / E-Mail</label>
                <input type="text" id="uname" name="username">
            </div>
            <div class="field-group">
                <label for="password">Password:</label>
                <input type="password" id="pwd" name="password">
            </div>
            <div class="field-group">
                <input type="checkbox" id="rem" name="remember">
                <label for="rem">Remember login credentials</label>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        <div class="btn-group">
            <span class="note">
                Already have an account? <a href="register.php" class="text-link">Register Now</a>
            </span><br><br>
        </div>
    </main>
</body>

</html>