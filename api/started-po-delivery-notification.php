<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$conn = mysqli_connect('127.0.0.1:3306', 'u733671518_wibs', '|4Kh/3XYD', 'u733671518_project');

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['po_id'], $_POST['delivery_reference_number'])) {
        $poId = $_POST['po_id'] ?? "";
        $deliveryReferenceNumber = $_POST['delivery_reference_number'] ?? "";

        // Store PO ID and Delivery Reference Number in the database
        storeReferenceNumber($conn, $poId, $deliveryReferenceNumber);

        // Fetch delivery status from API
        $deliveryStatus = fetchDeliveryStatusFromAPI($deliveryReferenceNumber);

        if ($deliveryStatus === 3) {
            // Update order status in database
            updateOrderStatus($conn, $poId);
        }
    }
}

function storeReferenceNumber($conn, $poId, $deliveryReferenceNumber) {
    mysqli_begin_transaction($conn);
    try {
        $sql = "INSERT INTO order_delivery_info (po_id, delivery_reference_number) VALUES ('$poId', '$deliveryReferenceNumber')";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error: " . $sql . "<br>" . mysqli_error($conn));
        }
        mysqli_commit($conn);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo $e->getMessage();
    }
}

function fetchDeliveryStatusFromAPI($deliveryReferenceNumber) {
    $apiUrl = 'https://cybertechlogistic.online/app/controller/get-delivery-history-api.php';
    $context = stream_context_create(['http' => ['method' => 'GET']]);

    $response = file_get_contents($apiUrl . '?delivery_reference_number=' . $deliveryReferenceNumber, false, $context);

    if ($response === FALSE) {
        // Handle error, maybe log it and return a default value
        return null;
    }

    $responseArray = json_decode($response, true);

    // Assuming the latest status is the last element of the array
    $latestStatus = end($responseArray)['status'];

    return $latestStatus;
}

function updateOrderStatus($conn, $poId) {
    mysqli_begin_transaction($conn);
    try {
        $sqlUpdate = "UPDATE purchase_orders SET status = 3 WHERE po_id = '$poId'";
        if (!mysqli_query($conn, $sqlUpdate)) {
            throw new Exception("Error: " . $sqlUpdate . "<br>" . mysqli_error($conn));
        }
        mysqli_commit($conn);
        echo "Order status updated successfully";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo $e->getMessage();
    }
}

mysqli_close($conn);
?>
