<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['user_id'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <h1>User Profile</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>

    <div id="viewDiv">
        <p>Name: <?php echo htmlspecialchars($firstname) . ' ' . htmlspecialchars($lastname); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Address: <?php echo htmlspecialchars($address); ?></p>
        <p>Mobile Number: <?php echo htmlspecialchars($mobile_num); ?></p>
        <p>Vrzn Account Number: <?php echo htmlspecialchars($vrzn_num); ?></p>
        <p>Apex Account Number: <?php echo htmlspecialchars($apex_num); ?></p>

        <button onclick="toggleEditForm()">Edit Profile</button>
    </div>

    <form id="editForm" method="POST" action="profile.php" style="display:none;">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">

        <label for="mobile_num">Mobile Number:</label>
        <input type="text" id="mobile_num" name="mobile_num" value="<?php echo htmlspecialchars($mobile_num); ?>">

        <label for="vrzn_num">VRZN Number:</label>
        <input type="text" id="vrzn_num" name="vrzn_num" value="<?php echo htmlspecialchars($vrzn_num); ?>">

        <label for="apex_num">APEX Number:</label>
        <input type="text" id="apex_num" name="apex_num" value="<?php echo htmlspecialchars($apex_num); ?>">

        <input type="submit" value="Update Profile">
        <button type="button" onclick="toggleEditForm()">Cancel</button>
    </form>

    <a href="homepage.php">Back to Home</a>
</body>
<script src="../js/profile.js"></script>
</html>
