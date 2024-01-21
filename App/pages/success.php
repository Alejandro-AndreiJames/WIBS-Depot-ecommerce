<?php
    if (isset($_GET['fund_transfer_success']) && $_GET['fund_transfer_success'] == 'true') {
        // Retrieve the po_id
        $po_id = $_SESSION['po_id']; // Replace with your method of retrieving the po_id
        ?>
    
        <!-- Global variable for JavaScript -->
        <script type="text/javascript">
            var poId = <?php echo json_encode($po_id); ?>;
        </script>
    
        <!-- Include External JavaScript file -->
        <script src="../js/status_update.js"></script>
        <?php
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Page</title>
</head>
<body>
    <h1>The Transaction is a Success</h1>
    <a href="./order_status.php">Back to Order Status</a>
</body>
</html>