<?php
session_start();
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['po_id'])) {
    $po_id = $_POST['po_id'];
    error_log("Received PO ID: " . $po_id);
    include 'db_conn.php';

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $update_query = "UPDATE purchase_orders SET status = 2 WHERE po_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $po_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update transaction status']);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
