<?php
include 'db_conn.php';

if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['quantity'];

    // Update the quantity in the database
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $stmt->bind_param("ii", $new_quantity, $cart_id);
    if ($stmt->execute()) {
        // Calculate new total amount
        $result = $conn->query("SELECT SUM(item_price * quantity) AS total FROM cart WHERE user_id = '$_SESSION[user_id]'");
        $row = $result->fetch_assoc();
        $newTotal = $row['total'];

        echo json_encode(['success' => true, 'newTotal' => $newTotal]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}
?>
