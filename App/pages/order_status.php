<?php
    session_set_cookie_params([
        'lifetime' => 0, // or a specific lifetime
        'path' => '/', // accessible across the entire domain
        'domain' => 'www.wibs.tech', // replace with your domain
        'secure' => true, // set to true if using HTTPS
        'httponly' => true // helps mitigate XSS attacks
    ]);
    session_start();
    include 'db_conn.php';

    if (!isset($_COOKIE['user_id'])) {
        $userId = $_COOKIE['user_id'];
        header("Location: login.php"); // Adjust the path as necessary
        exit;
        
    }

    // Accessing the username and user ID from the session variables
    $username = $_SESSION['user_name'];
    $userId = $_SESSION['user_id'];  // Assuming the user ID is stored in the session

    $statusMap = [
        1 => 'To Pay',
        2 => 'To Ship',
        3 => 'To Receive'
    ];

    $bankAccountQuery = "SELECT vrzn_num, apex_num FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($bankAccountQuery);
    $stmt->bind_param("i", $userId); // Changed to $userId
    $stmt->execute();
    $stmt->bind_result($vrzn_num, $apex_num);
    $stmt->fetch();
    $stmt->close();

    $source_account_no = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_bank'])) {
        $source_account_no = $_POST['selected_bank'] === 'vrzn' ? $vrzn_num : $apex_num;
    }


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
            // Select the appropriate icon based on the status
            $iconMap = [
                1 => 'icon1.png',
                2 => 'icon2.png',
                3 => 'icon3.png'
            ];
            $iconFile = $iconMap[$numericStatus];
    
            echo "<div class='no-orders'>";
            echo "<img src='../ASSETS/$iconFile' alt='No Orders' />";
            echo "<div class='text'><p>No orders found with status " . $statusMap[$numericStatus] . ".</p></div>";
            echo "</div>";
        } else {
            // Display each order
            while ($order = $result->fetch_assoc()) {
                if ($numericStatus == 1) {
                    echo '<div class="order" onclick="openPaymentModal(' . $order['po_id'] . ', \'' . $order['grand_total'] . '\')">';                
                } else {
                    echo '<div class="order" onclick="openModal(' . $order['po_id'] . ', \'' . $order['delivery_reference_number'] . '\')">';
                }
    
                // Decode the JSON data from the 'items' column
                $items = json_decode($order['items'], true);
    
                // Check if items data is available and is an array
                if (is_array($items)) {
                    echo '<div class="order-items">';
                    foreach ($items as $item) {
                        // Fetch item name from the API
                        $apiUrl = "https://www.thefusionseller.online/api_endpoints/get_item.php?item_id=" . $item['item_id'];
                        $apiResponse = file_get_contents($apiUrl);
                        $apiData = json_decode($apiResponse, true);
        
                        $itemName = isset($apiData[0]['item_name']) ? $apiData[0]['item_name'] : 'Unknown Item';
        
                        echo '<p>' . htmlspecialchars($itemName) . '</p>';
                        echo '<p>Quantity: ' . htmlspecialchars($item['qty']) . '</p>';
                        echo '<p>Price: ' . htmlspecialchars($item['price']) . '</p>';

                        echo '--------------------------------------------------------------------';
                    }
                    echo '<p class="total-amount">Total Amount: ' . htmlspecialchars($order['grand_total']) . '</p>';
                    echo '</div>';
                } else {
                    echo '<p>No item details available.</p>';
                }

                if ($numericStatus == 1) {
                    echo '<button onclick="deleteOrder(' . $order['po_id'] . ', event)">Cancel</button>'; 
                }
    
                echo '</div>';
            }
        }

        if (isset($_GET['po_id']) && $_GET['po_id']) {
                $string = $_GET['po_id'];

                $string_explode = explode('?', $string);
                $po_id1 = $string_explode[0];
                
                // Initialize $po_id to null
                $po_id = null;

                if (isset($_GET['po_id'])) 
                    $po_id = $_GET['po_id'];

                elseif (isset($_SESSION['po_id'])) {
                    $po_id = $_SESSION['po_id'];
                }
                if ($po_id !== null) {
                ?>
                <!-- Global variable for JavaScript -->
                <script type="text/javascript">
                    var poId = <?php echo json_encode($po_id); ?>;
                </script>
                
                <!-- Include External JavaScript file -->
                <script src="../js/status_update.js"></script>
                <?php
            }
        }

        if (isset($_GET['fund_transfer_success']) && $_GET['fund_transfer_success'] == 'true') {

            // Initialize $po_id to null
            $po_id = null;

            if (isset($_GET['po_id'])) 
                $po_id = $_GET['po_id'];

            elseif (isset($_SESSION['po_id'])) {
                $po_id = $_SESSION['po_id'];
            }
            if ($po_id !== null) {
            ?>
            <!-- Global variable for JavaScript -->
            <script type="text/javascript">
                var poId = <?php echo json_encode($po_id); ?>;
            </script>
            
            <!-- Include External JavaScript file -->
            <script src="../js/status_update.js"></script>
            <?php
        }
    }
        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
    
    function hasItemsInCart($userId) {
        global $conn;
    
        $query = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    
        return $count > 0;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <link rel="stylesheet" href="../css/order_status.css">
    <link rel="icon" type="image/png" href="../ASSETS/wd.png" />
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
                <a href="cart.php">My Cart<?php if (hasItemsInCart($userId)) echo '<span class="red-dot"></span>'; ?></a>
            </div>
 
            <div class="profile-name">
                <strong><?php echo $username?></strong>
            <div class="dropdown">
            <button class="dropbtn" onclick="toggleDropdown()">▼</button>
            <div class="dropdown-content" id="myDropdown">
                <a href="profile.php">View Profile</a>
                <a href="#" onclick="document.getElementById('logout-form').submit(); return false;">Logout</a>
                <form id="logout-form" action="logout.php" method="post" style="display: none;">
                    <input type="hidden" name="logout" value="1">
                </form>
            </div>
            </div>
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
                <h2>To Receive</h2>
                <?php displayOrders(3, $userId); ?>
            </div>
        </div>

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 class="order-header">Order Details</h2>
                <div id="orderDetails"></div>
            </div>
        </div>

        <div id="paymentModal" class="modal">
            <div class="modal-content">
                <div id="poIdElement" style="display:none;"></div>
                <span class="close" onclick="closePaymentModal()">&times;</span>
                <h2 class="pay-modal">To Pay</h2>
                <p class="item-details"><p>
                <p class="total-price">Total Price:</p>
                <p id="modalGrandTotal"></p>
                <button class="bank-button vrzn">
                    <img src="../ASSETS/vrzn_logo.png" alt="VRZN Logo" class="bank-logo" id="vrznButton" onclick="payment('vrzn')" >
                    VRZN
                </button>
                <button class="bank-button apex">
                    <img src="../ASSETS/apex_logo.png" alt="APEX Logo" class="bank-logo" id="apexButton" onclick="payment('apex')" >
                    APEX
                </button>
                <div style="display: none;">
                    <p id="modalBankCode"></p>
                    <p id="modalRecipientNumber"></p>
                    <div id="vrznAccountNo"><?php echo htmlspecialchars($vrzn_num); ?></div>
                    <div id="apexAccountNo"><?php echo htmlspecialchars($apex_num); ?></div>
                </div>
            </div>
        </div>
    
    <footer class="site-footer">
            <p>&copy; 2023 WIBS. All rights reserved.</p>
    </footer>
    <script src="../js/order_status.js"></script>
    <script src="../js/to_pay.js"></script>
    </body>
    </html>