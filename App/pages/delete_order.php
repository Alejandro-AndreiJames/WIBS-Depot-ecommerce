<?php
session_start();
include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['po_id'])) {
    $poId = $_POST['po_id'];
    $stmt = $conn->prepare("DELETE FROM purchase_orders WHERE po_id = ?");
    $stmt->bind_param("i", $poId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete order.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
$conn->close();
?>
