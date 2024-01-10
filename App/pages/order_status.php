<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php"); // Adjust the path as necessary
    exit;
}

// Accessing the username from the session variable
$username = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Status</title>
  <link rel="stylesheet" href="../css/order_status.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
</head>


<body>
  <div class="navbar">
        <div class="logo">WIBS</div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="order_status.php">Order Status</a>
            <a href="cart.php">My Cart</a>
        </div>
        <div class="profile-name">
            <strong><?php echo $username?></strong>
            |
            <form action="logout.php" method="post">
                <input type="submit" value="Logout">
            </form>
        </div>
   </div>

    <div class="status-text">
      <h1>Your Current Delivery Status</h1>
    </div>

    <div class="container">
        <div class="column" id="to-pay" onclick="filterOrders('To Pay')">
            <h2>To Pay</h2>
            <?php displayOrders('To Pay'); ?>
        </div>
        <div class="column" id="to-ship" onclick="filterOrders('To Ship')">
            <h2>To Ship</h2>
            <?php displayOrders('To Ship'); ?>
        </div>
        <div class="column" id="completed" onclick="filterOrders('Completed')">
            <h2>Completed</h2>
            <?php displayOrders('Completed'); ?>
        </div>
    </div>

<?php
    function displayOrders($status)
{
    $orders = [
        ['po_id' => '1', 'user_id' => '2', 'grand_total' => '18990.00', 'customer_name' => 'sdfres13', 'delivery_address' => 'Blk 31 Lot 15 Taguig City', 'status' => 'To Pay'],
        ['po_id' => '5', 'user_id' => '1', 'grand_total' => '77490.00', 'customer_name' => 'Hassanda', 'delivery_address' => 'Pasay City', 'status' => 'Completed'],
        ['po_id' => '6', 'user_id' => '3', 'grand_total' => '19290.00', 'customer_name' => 'Peachyyy', 'delivery_address' => '209 Aguirre StreetB F Homes 1700', 'status' => 'Completed'],
        ['po_id' => '7', 'user_id' => '6', 'grand_total' => '6490.00', 'customer_name' => 'yvesuuu', 'delivery_address' => 'G/F Elizabeth Mall, Leon Kilat corner South Expressway', 'status' => 'To Ship'],
        ['po_id' => '8', 'user_id' => '4', 'grand_total' => '48500.00', 'customer_name' => 'aerish', 'delivery_address' => '1680 Commandante 1000', 'status' => 'Completed'],
        ['po_id' => '2', 'user_id' => '5', 'grand_total' => '35000.00', 'customer_name' => 'ba0zi', 'delivery_address' => 'Andres Bonifacio Avenue, Bagting', 'status' => 'To Pay'],
        // Add more orders as needed
    ];

    foreach ($orders as $order) {
        if ($order['status'] == $status) {
            echo '<div class="order" onclick="openModal(' . $order['po_id'] . ')">';
            echo '<p>Order No. ' . $order['po_id'] . '</p>';
            echo '<p>User id: ' . $order['user_id'] . '</p>';
            echo '<p>Grand Total: ' . $order['grand_total'] . '</p>';
            echo '<p>Customer Name: ' . $order['customer_name'] . '</p>';
            echo '<p>Delivery Address: ' . $order['delivery_address'] . '</p>';
            echo '</div>';
        }
    }
}
?>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Order Details</h2>
            <div id="orderDetails"></div>
        </div>
    </div>

  <footer class="site-footer">
        <p>&copy; 2023 WIBS. All rights reserved.</p>
  </footer>
  <script src="order_status.js"></script>
</body>
</html>