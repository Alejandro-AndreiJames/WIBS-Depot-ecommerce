    <?php
    session_start();

    // Check if the user is logged in, if not then redirect to login page
    if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Adjust the path as necessary
        exit;
    }

    // Accessing the username and user ID from the session variables
    $username = $_SESSION['user_name'];
    $userId = $_SESSION['user_id'];  // Assuming the user ID is stored in the session

    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $statusMap = [
        1 => 'To Pay',
        2 => 'To Ship',
        3 => 'Completed'
    ];

    function displayOrders($numericStatus, $userId) {
        global $statusMap;

        // Include database connection
        include 'db_conn.php';

        // SQL query to fetch orders based on status and user ID
        $sql = "SELECT purchase_orders.*, order_delivery_info.delivery_reference_number FROM purchase_orders LEFT JOIN order_delivery_info ON purchase_orders.po_id = order_delivery_info.po_id WHERE purchase_orders.status = ? AND purchase_orders.user_id = ?";

        // Prepare statement
        $stmt = $conn->prepare($sql);

        // Check if the statement was prepared correctly
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters and execute query
        $stmt->bind_param("ii", $numericStatus, $userId);
        $stmt->execute();

        // Check for errors in execution
        if ($stmt->error) {
            die("Error executing statement: " . $stmt->error);
        }

        $result = $stmt->get_result();

        // Check if any orders are found
        if ($result->num_rows == 0) {
            echo "No orders found for user ID $userId with status " . $statusMap[$numericStatus] . ".";
        } else {
            // Display each order
            while ($order = $result->fetch_assoc()) {
                echo '<div class="order" onclick="openModal(' . $order['po_id'] . ', \'' . $order['delivery_reference_number'] . '\')">';
                echo '<p>Order No. ' . $order['po_id'] . '</p>';
                echo '<p>User id: ' . $order['user_id'] . '</p>';
                echo '<p>Grand Total: ' . $order['grand_total'] . '</p>';
                echo '<p>Customer Name: ' . $order['customer_name'] . '</p>';
                echo '<p>Delivery Address: ' . $order['delivery_address'] . '</p>';
                echo '<p>Status: ' . $statusMap[$numericStatus] . '</p>'; // Display textual status
                echo '</div>';
            }
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
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
        <div class="logo">
            <img src="../ASSETS/wibsdepot2.png" alt="logo">
        </div>
            <div class="nav-links">
                <a href="homepage.php">Home</a>
                <a href="order_status.php">Order Status</a>
                <a href="cart.php">My Cart</a>
            </div>
            <div class="profile-name">
                <strong><?php echo htmlspecialchars($username); ?></strong>
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
            <div class="column" id="to-pay">
                <h2>To Pay</h2>
                <?php displayOrders(1, $userId); ?>
            </div>
            <div class="column" id="to-ship">
                <h2>To Ship</h2>
                <?php displayOrders(2, $userId); ?>
            </div>
            <div class="column" id="completed">
                <h2>Completed</h2>
                <?php displayOrders(3, $userId); ?>
            </div>
        </div>

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
    <script src="../js/order_status.js"></script>
    </body>
    </html>
