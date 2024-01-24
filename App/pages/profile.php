<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
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

// Check if the user's profile information has been updated recently
$profileUpdated = isset($_SESSION['profile_updated']) && $_SESSION['profile_updated'];

// Unset the session variable to prevent showing the message again on refresh
unset($_SESSION['profile_updated']);

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


if ($message === "Information updated successfully!") {
    $_SESSION['profile_updated'] = true;
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
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
        <h1>My Profile</h1>

        <img src="../ASSETS/prof-icon.png" alt="Profile Picture" class="profile-pic" />
        
        <?php if ($profileUpdated): ?>
        <p class="profile-message">Information updated successfully!</p>
    <?php endif; ?>

        <h2 class="username"><?php echo htmlspecialchars($firstname) . ' ' . htmlspecialchars($lastname); ?></h2>
        
        <div class="user-details">
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <p>Home Address: <?php echo htmlspecialchars($address); ?></p>
            <p>Mobile Number: <?php echo htmlspecialchars($mobile_num); ?></p>
            <p>VRZN Account Number: <?php echo htmlspecialchars($vrzn_num); ?></p>
            <p>APEX Account Number: <?php echo htmlspecialchars($apex_num); ?></p>
        </div>
        
        <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
    </div>

    <footer class="site-footer">
        <p>&copy; 2023 WIBS. All rights reserved.</p>
    </footer>

    <script src="../js/profile.js"></script>
</body>
</html>
