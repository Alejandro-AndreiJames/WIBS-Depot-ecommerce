<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:*");
header("Access-Control-Allow-Headers:*");

/* if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST parameters
    $po_id = isset($_POST['po_id']) ? $_POST['po_id'] : null;
    $delivery_reference_number = isset($_POST['delivery_reference_number']) ? $_POST['delivery_reference_number'] : null;
    
    // Perform input validation
    if (is_null($po_id) || is_null($delivery_reference_number)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters.']);
        exit;
    }

    // Database configuration
    $mysqli = new mysqli('127.0.0.1:3306', 'u733671518_wibs', '|4Kh/3XYD', 'u733671518_project');

    // Check connection
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo json_encode(['result' => 'error', 'message' => 'Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error]);
        exit;
    }

    // Start transaction
    $mysqli->begin_transaction();

    try {
        // Prepare SQL statement to update the delivery status
        $stmtUpdate = $mysqli->prepare("UPDATE purchase_orders SET status = '3' WHERE po_id = ?");
        $stmtUpdate->bind_param('i', $po_id);

        // Execute the statement
        if (!$stmtUpdate->execute()) {
            throw new Exception('Execute Error: ' . $stmtUpdate->error);
        }

        // Insert into order_delivery_info table
        $stmtInsert = $mysqli->prepare("INSERT INTO order_delivery_info (po_id, delivery_reference_number) VALUES (?, ?)");
        $stmtInsert->bind_param('is', $po_id, $delivery_reference_number);

        if (!$stmtInsert->execute()) {
            throw new Exception('Execute Error: ' . $stmtInsert->error);
        }

        // Commit transaction
        $mysqli->commit();

        // If everything is fine, return a success message
        http_response_code(200);
        echo json_encode(['result' => 'success', 'message' => 'Delivery status updated successfully.']);

    } catch (Exception $e) {
        // Rollback transaction on error
        $mysqli->rollback();
        http_response_code(500);
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    } finally {
        // Close statements and connection
        $stmtUpdate->close();
        $stmtInsert->close();
        $mysqli->close();
    }
} else {
    // If the request is not a POST, return a 405 Method Not Allowed
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed.']);
}
?>*/

$conn = mysqli_connect('127.0.0.1:3306','u733671518_wibs','|4Kh/3XYD','u733671518_project');
#$conn = mysqli_connect('localhost','root','','u733671518_project');

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['po_id'], $_POST['delivery_reference_number'])) {

        $poId = $_POST['po_id'] ?? "";
        $deliveryReferenceNumber = $_POST['delivery_reference_number'] ?? "";

        // Begin transaction
        mysqli_begin_transaction($conn);

        // Insertion query
        $insertSql = "INSERT INTO order_delivery_info (po_id, delivery_reference_number) VALUES ('$poId', '$deliveryReferenceNumber')";
        if (!mysqli_query($conn, $insertSql)) {
            echo "Error: " . $insertSql . "<br>" . mysqli_error($conn);
            mysqli_rollback($conn);
        } else {
            // Update query
            $updateSql = "UPDATE purchase_order SET status = 3 WHERE id = '$poId'";
            if (!mysqli_query($conn, $updateSql)) {
                echo "Error: " . $updateSql . "<br>" . mysqli_error($conn);
                mysqli_rollback($conn);
            } else {
                // Commit transaction
                mysqli_commit($conn);
                echo "New record created successfully and po_id status updated";
            }
        }
    }
    mysqli_close($conn);
}


