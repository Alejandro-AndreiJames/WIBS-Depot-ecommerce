<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:*");
header("Access-Control-Allow-Headers:*");

$conn = mysqli_connect('127.0.0.1:3306','u733671518_wibs','|4Kh/3XYD','u733671518_project');

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['po_id'], $_POST['delivery_reference_number'])) {

        $poId = $_POST['po_id'] ?? "";
        $deliveryReferenceNumber = $_POST['delivery_reference_number'] ?? "";

        mysqli_begin_transaction($conn);

        try {
            $sql = "INSERT INTO order_delivery_info (po_id, delivery_reference_number) VALUES ('$poId', '$deliveryReferenceNumber')";
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error: " . $sql . "<br>" . mysqli_error($conn));
            }

            $sqlUpdate = "UPDATE purchase_orders SET status = 3 WHERE po_id = $poId";
            if (!mysqli_query($conn, $sqlUpdate)) {
                throw new Exception("Error: " . $sqlUpdate . "<br>" . mysqli_error($conn));
            }

            mysqli_commit($conn);
            echo "New record created successfully";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo $e->getMessage();
        }
    }
    mysqli_close($conn);
}