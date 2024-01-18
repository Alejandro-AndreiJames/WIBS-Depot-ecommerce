<?php
session_start();
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:*");
header("Access-Control-Allow-Headers:*");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
            <p>Bank Code: <span id="bankCode">[Bank Code]</span></p>
            <p>Recipient Number: <span id="recipientNumber">[Recipient Number]</span></p>
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
            <div id="poIdElement" data-po-id="<?php echo $_SESSION['po_id']; ?>" style="display:none;"></div> 
            <div id="transactionAmount"><?php echo htmlspecialchars($total_amount); ?></div>
            <div id="vrznAccountNo"><?php echo htmlspecialchars($vrzn_num); ?></div>
            <div id="apexAccountNo"><?php echo htmlspecialchars($apex_num); ?></div>
        </div>
    </form>
    <script src="../js/payment_selection.js"></script>
    <button id="testButton">Test Update Status</button>
</body>
</html>
