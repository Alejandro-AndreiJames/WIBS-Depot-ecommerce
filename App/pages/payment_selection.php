<?php
  session_set_cookie_params([
    'lifetime' => 0, // or a specific lifetime
    'path' => '/', // accessible across the entire domain
    'domain' => 'wibs.tech', // replace with your domain
    'secure' => true, // set to true if using HTTPS
    'httponly' => true // helps mitigate XSS attacks
]);
session_start();
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:*");
header("Access-Control-Allow-Headers:*");

if (!isset($_COOKIE['user_id'])) {
    $userId = $_COOKIE['user_id'];
    header("Location: login.php"); // Adjust the path as necessary
    exit;
    
}

include 'db_conn.php';

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
    <link rel="stylesheet" href="../css/payment_selection.css">
</head>
<body>
    <div class="header">
        <h1>Payment Selection</h1>
    </div>

    <form method="POST" action="">
        <div class="payment-details">
            <strong><p>Pay to: <span id="recipientName">FusionDesign</span></p></strong>
            <strong><p>₱<span id="totalAmount"><?php echo htmlspecialchars($total_amount); ?></span></p></strong>
            <p>Select Bank:</p>
            <div style="display: none;">
            <p>Bank Code: <span id="bankCode">[Bank Code]</span></p>
            <p>Recipient Number: <span id="recipientNumber">[Recipient Number]</span></p>
            </div>
        </div>
        <div class="bank-selection">
        <button type="submit" id="vrznButton" name="selected_bank" value="vrzn" class="bank-btn">
            <img src="../ASSETS/vrzn_logo.png" alt="Vrzn Bank" class="bank-logo">
        </button>
        <button type="submit" id="apexButton" name="selected_bank" value="apex" class="bank-btn">
            <img src="../ASSETS/apex_logo.png" alt="Apex Bank" class="bank-logo">
        </button>
        </div>
        <div style="display: none;">
            <div id="transactionAmount"><?php echo htmlspecialchars($total_amount); ?></div>
            <div id="vrznAccountNo"><?php echo htmlspecialchars($vrzn_num); ?></div>
            <div id="apexAccountNo"><?php echo htmlspecialchars($apex_num); ?></div>
        </div>
    </form>
    <footer class="site-footer">
        <p>&copy; 2023 WIBS. All rights reserved.</p>
    </footer>
    <script src="../js/payment_selection.js"></script>
</body>
</html>
