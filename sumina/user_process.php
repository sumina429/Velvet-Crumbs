<?php
include 'config/db.php';
include 'config/session.php';
$debug_mode = 1;

// LOGIN PROCESS
if (isset($_GET['login'])) {
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';
    $remember = isset($_GET['remember']) ? $_GET['remember'] : '';

    if ($debug_mode) {
        echo "username: " . htmlspecialchars($username) . "<br>";
        echo "password: " . htmlspecialchars($password) . "<br>";
        echo "remember: " . htmlspecialchars($remember) . "<br>";
    }

    // Check if user exists in database (allow username or email for login)
    $query = "SELECT * FROM users WHERE username='$username' OR email='$username' LIMIT 1";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            login_to($user['username'], $user['role'], $user['uid']);
            if ($_SESSION['role'] == "admin") {
                header("Location: admin/overview.php");
                exit();
            }
            if ($_SESSION['role'] == "customer") {
                header("Location: index.php");
                exit();
            }
        } else {
            echo "<div>Invalid password.</div>";
            $_SESSION['login_message'] = "Invalid password";
            header("Location: login.php");
            exit();
        }
    } else {
        echo "<div>User not found.</div>";
        $_SESSION['login_message'] = "User not found";
        header("Location: login.php");
        exit();
    }
}

// REGISTRATION PROCESS
if (isset($_GET['register'])) {
    // Get variables as per DB columns
    $fname = isset($_GET['fullname']) ? $_GET['fullname'] : ''; // 'fullname' field maps to 'fname'
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';
    $confirm_password = isset($_GET['confirm_password']) ? $_GET['confirm_password'] : '';
    $agree = isset($_GET['agree']) ? $_GET['agree'] : 0;
    // Fetch role id, if not provided, default to 'customer'
    $role = isset($_GET['role']) && !empty($_GET['role']) ? $_GET['role'] : 'customer';

    // These values would be blank as the form does not collect them (but set for DB structure)
    $address = '';
    $contact = '';
    $gender = '';
    $dob = null;

    if ($debug_mode) {
        echo "fname: " . htmlspecialchars($fname) . "<br>";
        echo "username: " . htmlspecialchars($username) . "<br>";
        echo "email: " . htmlspecialchars($email) . "<br>";
        echo "password: " . htmlspecialchars($password) . "<br>";
        echo "confirm_password: " . htmlspecialchars($confirm_password) . "<br>";
        echo "agree: " . htmlspecialchars($agree) . "<br>";
    }

    // Validate input
    $errors = array();

    if (empty($fname))
        $errors[] = "Full name is required.";
    if (empty($username))
        $errors[] = "Username is required.";
    if (empty($email))
        $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password))
        $errors[] = "Password is required.";
    if (empty($confirm_password))
        $errors[] = "Please confirm your password.";
    if ($password !== $confirm_password)
        $errors[] = "Passwords do not match.";
    if (!$agree)
        $errors[] = "You must agree to the Terms of Service.";

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $existing = mysqli_query($db, "SELECT uid FROM users WHERE username='$username' OR email='$email' LIMIT 1");
    if ($existing && mysqli_num_rows($existing) > 0) {
        $err_user = mysqli_fetch_assoc($existing);
        if ($err_user && $err_user['uid']) {
            $errors[] = "Username or email already exists.";
        }
    }

    if (count($errors) > 0) {
        foreach ($errors as $err) {
            echo "<div>$err</div>";
        }
    } else {
        // Insert new user using all the columns we have from the form/data for DB
        $query = "INSERT INTO users (username, password, role, fname, address, contact, gender, dob, email, created_at, updated_at) 
                  VALUES (
                      '$username', 
                      '$hashed_password', 
                      '$role', 
                      '$fname', 
                      '$address', 
                      '$contact', 
                      '$gender', 
                      " . ($dob ? "'$dob'" : "NULL") . ", 
                      '$email',
                      NOW(), 
                      NOW()
                  )";
        if (mysqli_query($db, $query)) {
            echo "<div>Registration successful! You can now <a href='login.php'>login</a>.</div>";
        } else {
            echo "<div>Error registering user: " . htmlspecialchars(mysqli_error($db)) . "</div>";
        }
    }
}
?>