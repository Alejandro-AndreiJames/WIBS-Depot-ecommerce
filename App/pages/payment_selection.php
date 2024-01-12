<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'u733671518_project');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$total_amount = $_SESSION['total_amount'];

$bankAccountQuery = "SELECT vrzn_num, apex_num FROM customer WHERE customer_id = ?";
$stmt = $conn->prepare($bankAccountQuery);
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$stmt->bind_result($vrzn_num, $apex_num);
$stmt->fetch();
$stmt->close();

$source_account_no = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_bank'])) {
    $source_account_no = $_POST['selected_bank'] === 'vrzn' ? $vrzn_num : $apex_num;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Selection</title>
</head>
<body>

    <form method="POST" action="">
        <input type="radio" id="vrzn" name="selected_bank" value="vrzn" required>
        <label for="vrzn">Vrzn Bank</label><br>
        <input type="radio" id="apex" name="selected_bank" value="apex">
        <label for="apex">Apex Bank</label><br>
        <button type="submit" name="pay">Pay</button>
        <div style="display: none;">
            <div id="transactionAmount"><?php echo htmlspecialchars($total_amount); ?></div>
            <div id="vrznAccountNo"><?php echo htmlspecialchars($vrzn_num); ?></div>
            <div id="apexAccountNo"><?php echo htmlspecialchars($apex_num); ?></div>
        </div>
    </form>
    <script src="../js/payment_selection.js"></script>
</body>
</html>
