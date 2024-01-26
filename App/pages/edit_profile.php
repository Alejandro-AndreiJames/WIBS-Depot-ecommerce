<?php
  session_set_cookie_params([
    'lifetime' => 0, // or a specific lifetime
    'path' => '/', // accessible across the entire domain
    'domain' => 'wibs.tech', // replace with your domain
    'secure' => true, // set to true if using HTTPS
    'httponly' => true // helps mitigate XSS attacks
]);
session_start();
include 'db_conn.php';

if (!isset($_COOKIE['user_id'])) {
    $userId = $_COOKIE['user_id'];
    header("Location: login.php"); // Adjust the path as necessary
    exit;
    
}

$userid = $_SESSION['user_id'];
$username = $_SESSION['user_name'];
$message = '';

function updateUserDetails($conn, $userid, $firstname, $lastname, $email, $address, $mobile_num, $vrzn_num, $apex_num) {
    $query = "UPDATE customer SET firstname=?, lastname=?, email=?, address=?, mobile_num=?, vrzn_num=?, apex_num=? WHERE customer_id=?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssssssi", $firstname, $lastname, $email, $address, $mobile_num, $vrzn_num, $apex_num, $userid);
        if ($stmt->execute()) {
            return "Information updated successfully!";
        } else {
            return "Error updating record: " . $conn->error;
        }
        $stmt->close();
    } else {
        return "Error preparing statement: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $mobile_num = $_POST['mobile_num'];
    $vrzn_num = $_POST['vrzn_num'];
    $apex_num = $_POST['apex_num'];

    $message = updateUserDetails($conn, $userid, $firstname, $lastname, $email, $address, $mobile_num, $vrzn_num, $apex_num);
}

// Fetch user details from the database
$query = "SELECT firstname, lastname, email, address, mobile_num, vrzn_num, apex_num FROM customer WHERE customer_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($firstname, $lastname, $email, $address, $mobile_num, $vrzn_num, $apex_num);
    $stmt->fetch();
    $stmt->close();
}

$cartQuery = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($cartQuery);
$stmt->bind_param("i", $userid);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($cartCount);
$stmt->fetch();
$hasCartItems = $cartCount > 0;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/edit_profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link href="https://fonts.googleapis.com/css2?family=Poppins" rel="stylesheet">
</head>
<body>
    <div class="navbar">
            <div class="logo">
                <img src="../ASSETS/wibsdepot2.png" alt="logo">
            </div>

            <div class="nav-links">
                <a href="homepage.php">Home</a>
                <a href="order_status.php">Order Status</a>
                <a href="cart.php">My Cart<?php if ($hasCartItems) echo '<span class="red-dot"></span>'; ?></a>
            </div>

            <div class="profile-name">
                <strong><?php echo $username?></strong>
            <div class="dropdown">
            <button class="dropbtn" onclick="toggleDropdown()">▼</button>
            <div class="dropdown-content" id="myDropdown">
                <a href="profile.php">View Profile</a>
                <a href="#" onclick="document.getElementById('logout-form').submit(); return false;">Logout</a>
                <form id="logout-form" action="logout.php" method="post" style="display: none;">
                    <input type="hidden" name="logout" value="1">
                </form>
            </div>
            </div>
            </div>
    </div>

    <div class="profile-container">
        <h1>Edit Profile</h1>

        <img src="../ASSETS/prof-icon.png" alt="Profile Picture" class="profile-pic" />
    </div>


    <div class="edit-profile-container" id="editForm">
    <form method="POST" action="profile.php">
        <div class="form-row">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
        </div>

        <div class="form-row">
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
        </div>

        <div class="form-row">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-row">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
        </div>

        <div class="form-row">
            <label for="mobile_num">Mobile Number:</label>
            <input type="text" id="mobile_num" name="mobile_num" value="<?php echo htmlspecialchars($mobile_num); ?>">
        </div>

        <div class="form-row">
            <label for="vrzn_num">VRZN Number:</label>
            <input type="text" id="vrzn_num" name="vrzn_num" value="<?php echo htmlspecialchars($vrzn_num); ?>">
        </div>

        <div class="form-row">
            <label for="apex_num">APEX Number:</label>
            <input type="text" id="apex_num" name="apex_num" value="<?php echo htmlspecialchars($apex_num); ?>">
        </div>

        <div class="form-actions">
            <input type="submit" value="Update Profile" class="submit-btn">
            <button type="button"  onclick="cancelEdit()" class="cancel-btn">Cancel</button>
        </div>
    </form>
    </div>

    <footer class="site-footer">
        <p>&copy; 2023 WIBS. All rights reserved.</p>
    </footer>

    <script src="../js/profile.js"></script>
</body>
</html>
