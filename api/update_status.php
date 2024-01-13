<?php
$conn = mysqli_connect('localhost', 'root', '', 'u733671518_project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you are receiving JSON data
    $_POST = json_decode(file_get_contents('php://input'), true);

    // Retrieve PO ID from POST request
    $po_id = isset($_POST['po_id']) ? $_POST['po_id'] : '';

    // SQL to update PO status
    $sql = "UPDATE purchase_orders SET status='2' WHERE po_id=?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $po_id);

    // Execute and check the query
    if ($stmt->execute()) {
        echo "Status updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method";
}

?>