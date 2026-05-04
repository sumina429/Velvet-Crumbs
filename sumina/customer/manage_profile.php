<?php
include '../config/db.php';
include '../config/session.php';
isCustomer();

$uid = $_SESSION['uid'];

// Fetch user data
$sql = "SELECT * FROM users WHERE uid = $uid";
$result = mysqli_query($db, $sql);
$users = mysqli_fetch_assoc($result);

// Initialize variables for the form (with current values)
$username = isset($users['username']) ? $users['username'] : '';
$fname = isset($users['fname']) ? $users['fname'] : '';
$email = isset($users['email']) ? $users['email'] : '';
$address = isset($users['address']) ? $users['address'] : '';
$contact = isset($users['contact']) ? $users['contact'] : '';
$gender = isset($users['gender']) ? $users['gender'] : '';
$dob = isset($users['dob']) ? $users['dob'] : '';

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get fields from POST
    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $fname = mysqli_real_escape_string($db, trim($_POST['fname']));
    $email = mysqli_real_escape_string($db, trim($_POST['email']));
    $address = mysqli_real_escape_string($db, trim($_POST['address']));
    $contact = mysqli_real_escape_string($db, trim($_POST['contact']));
    $gender = mysqli_real_escape_string($db, trim($_POST['gender']));
    $dob = mysqli_real_escape_string($db, trim($_POST['dob']));

    // Basic validation
    if (empty($username) || empty($fname) || empty($email)) {
        $error = "Username, Full Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update query
        $update_q = "UPDATE users SET 
            username='$username',
            fname='$fname',
            email='$email',
            address='$address',
            contact='$contact',
            gender='$gender',
            dob='$dob'
            WHERE uid=$uid
        ";
        if (mysqli_query($db, $update_q)) {
            // Optionally update username in session
            $_SESSION['username'] = $username;
            // Redirect back to profile.php on success
            header("Location: profile.php");
            exit();
        } else {
            $error = "Update failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Profile - Velvet Crumbs</title>
    <link rel="stylesheet" href="./cs/profile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="brand"><img src="../logo.png" alt="logo"> <span>Velvet Crumbs</span></div>
        <div>
            <!-- Navigation -->
            <nav class="nav-links">
                <a href="../">Home</a>
                <a href="product.php">Products</a>
                <a href="../about.php">About</a>
            </nav>
        </div>
        <div class="header-actions">
            <div style="position: relative;">
                <a href="cart.php" style="text-decoration: none; color: inherit;">
                    <span style="font-size: 24px;">🛒</span>
                </a>
                <?php
                $sql_get_count = "SELECT count(*) as count FROM cart c JOIN products p ON c.pid = p.pid WHERE c.uid = {$_SESSION['uid']}";
                $result = mysqli_query($db, $sql_get_count);
                $data_count = mysqli_fetch_assoc($result);
                $count = $data_count['count'];
                if ($count) {
                    echo '<span id="cart-badge">' . $count . '</span>';
                }
                ?>
            </div>
            <a href="profile.php" class="user-pill">👤
                <?php echo $_SESSION['username']; ?>
            </a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </header>
    <div class="max-w-xl mx-auto mt-10 bg-white p-8 rounded-xl shadow-lg">
        <h1 class="text-[1.7rem] font-bold mb-3 text-[#b34d85]">Edit Profile</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="manage_profile.php" class="space-y-5">
            <div>
                <label class="block font-semibold mb-1">Username *</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
            </div>
            <div>
                <label class="block font-semibold mb-1">Full Name *</label>
                <input type="text" name="fname" value="<?php echo htmlspecialchars($fname); ?>" required class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
            </div>
            <div>
                <label class="block font-semibold mb-1">Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
            </div>
            <div>
                <label class="block font-semibold mb-1">Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
            </div>
            <div>
                <label class="block font-semibold mb-1">Contact</label>
                <input type="text" name="contact" value="<?php echo htmlspecialchars($contact); ?>" class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
            </div>
            <div>
                <label class="block font-semibold mb-1">Gender</label>
                <select name="gender" class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
                    <option value="">Select</option>
                    <option value="Male" <?php if ($gender == 'Male') echo "selected"; ?>>Male</option>
                    <option value="Female" <?php if ($gender == 'Female') echo "selected"; ?>>Female</option>
                    <option value="Other" <?php if ($gender == 'Other') echo "selected"; ?>>Other</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Date of Birth</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-[#b34d85]">
            </div>
            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-[#ec4899] hover:bg-[#b34d85] text-white font-semibold px-6 py-2 rounded-lg shadow transition min-w-[120px]">Update</button>
                <a href="profile.php" class="inline-block px-6 py-2 text-[#b34d85] border border-[#b34d85]  rounded-lg font-semibold hover:bg-[#fbe1ef] transition">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>