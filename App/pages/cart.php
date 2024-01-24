        <?php
            // Connect to the database
            include 'db_conn.php';

            session_start();

            $cartQuery = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($cartQuery);
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($cartCount);
            $stmt->fetch();
            $hasCartItems = $cartCount > 0;
            $stmt->close();

            // Check if the user is logged in, if not then redirect to login page
            if (!isset($_SESSION['user_name'])) {
                header("Location: login.php"); // Adjust the path as necessary
                exit;
            }

            // Accessing the username from the session variable
            $userid = $_SESSION['user_id'];
            $username = $_SESSION['user_name'];

            //update quantity
            if (isset($_POST['update_quantity']) && isset($_POST['cart_id'])) {
                $cart_id = $_POST['cart_id'];
                $new_quantity = $_POST['quantity'];

                $sql_update = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("ii", $new_quantity, $cart_id);
                if ($stmt->execute()) {
                    // Redirect back to refresh the page
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "Error updating record: " . $conn->error;
                }
                $stmt->close();
            }

            // Check if remove item request is set
            $itemRemoved = false; // Variable to track if an item was removed
            if (isset($_POST['remove_item']) && isset($_POST['cart_id'])) {
                $cart_id = $_POST['cart_id'];
                // SQL to remove item from cart
                $sql_remove = "DELETE FROM cart WHERE cart_id = ?";
                $stmt = $conn->prepare($sql_remove);
                $stmt->bind_param("i", $cart_id);
                if ($stmt->execute()) {
                    $itemRemoved = true; // Set to true if item is removed successfully
                } else {
                    echo "Error: " . $conn->error;
                }
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']); // Refresh page to update cart
                exit;
            }

            // Process the place order request
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
                // Retrieve customer name and delivery address from session
                $customer_name = $username;

                $address_query = "SELECT address FROM customer WHERE customer_id = '$userid'";
                $address_result = $conn->query($address_query);
                if ($address_row = $address_result->fetch_assoc()) {
                    $customer_address = $address_row['address'];
                } else {
                    $customer_address = 'Default Address'; // Fallback address
                }
                
                // Fetch cart items
                $sql = "SELECT item_id, quantity, item_price FROM cart WHERE user_id = '$userid'";
                $result = $conn->query($sql);
                $items = [];
                $grand_total = 0;
                while ($row = $result->fetch_assoc()) {
                    $total_price = $row['item_price'] * $row['quantity'];
                    $items[] = array(
                        'item_id' => $row['item_id'],
                        'qty' => $row['quantity'],
                        'price' => $row['item_price'],
                        'total_price' => $total_price
                    );
                    $grand_total += $total_price;
                }
                
                $items_json = json_encode($items);
                
                // Insert into purchase_orders
                $insert_sql = "INSERT INTO purchase_orders (user_id, items, grand_total, customer_name, delivery_address, status) VALUES ('$userid', '$items_json', $grand_total, '$customer_name', '$customer_address', 1)";
                if ($conn->query($insert_sql) === TRUE) {
                    echo "Order placed successfully";
                    $po_id = $conn->insert_id; // Retrieve the last inserted ID
                    $_SESSION['po_id'] = $po_id; 
                    $conn->query("DELETE FROM cart WHERE user_id = '$userid'");
                    header("Location: payment_selection.php");
                    exit();
                } else {
                    echo "Error: " . $conn->error;
                }
            }

            // Check if the required POST values are set for adding to cart
            if (isset($_POST['item_id'], $_POST['item_name'], $_POST['quantity'], $_POST['item_price'], $_POST['item_image'])) {
                // Get data from the client-side
                $item_id = $_POST['item_id'];
                $item_name = $_POST['item_name'];
                $quantity = $_POST['quantity'];
                $item_price = $_POST['item_price'];
                $item_image = $_POST['item_image'];

                // Insert data into the database including user_id and item_id
                $sql = "INSERT INTO cart (user_id, item_id, item_name, quantity, item_price, item_image) VALUES ('$userid', '$item_id', '$item_name', $quantity, $item_price, '$item_image')";

                if ($conn->query($sql) === TRUE) {
                    echo "Item added to cart successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            // Check if there are any items in the cart
            $cart_check_sql = "SELECT * FROM cart WHERE user_id = '$userid'";
            $cart_check_result = $conn->query($cart_check_sql);
            $hasCartItems = $cart_check_result->num_rows > 0;
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>My Cart</title>
            <link rel="stylesheet" href="../css/cart.css">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
        </head>
        <body>
        <div class="overlay"></div>
    <div class="navbar">
        <div class="logo">
            <img src="../ASSETS/wibsdepot2.png" alt="logo">
        </div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="order_status.php">Order Status</a>
            <a href="cart.php">My Cart<?php if ($hasCartItems) echo '<span class="red-dot"></span>'; ?></a>
        </div>
        <div class="profile-name">
            <strong><?php echo $username?></strong>
            |
            <form action="logout.php" method="post">
                <input type="submit" value="Logout">
            </form>
        </div>
    </div>
    <div id="itemRemoved" data-removed="<?php echo $itemRemoved ? 'true' : 'false'; ?>" style="display:none;"></div>
    <?php if ($itemRemoved): ?>
        <div class='notification' id='notification'>Item removed from cart successfully</div>
    <?php endif; ?>
            <div class="my-cart">
                <h1>My Cart</h1>
            </div>
            <div class="main">
                <div class="cart-list">
                <div class="cart-items">
                    <?php
                    $total_amount = 0;
                    // Fetch cart items from the database for the current user
                    $sql = "SELECT * FROM cart WHERE user_id = '$userid'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                         // Display cart items
                        while ($row = $result->fetch_assoc()) {
                            echo '<form class="item" action="" method="post">';
                            echo '<input type="hidden" name="update_quantity" value="1">';
                            echo '<input type="hidden" name="cart_id" value="' . $row['cart_id'] . '">';
                            echo '<div class="cart-item">';
                            echo '<div class="cart-item-image">';
                            echo '<img src="' . $row['item_image'] . '" alt="Item Image">';
                            echo '</div>';
                            echo '<div class="item-info">';
                            echo '<p>' . $row['item_name'] . '</p>';
                            echo '<p>₱ ' . $row['item_price'] . '</p>';
                            echo '</div>';
                            echo '<div class="quantity">';
                            echo '<button type="button" class="quantity-change" data-change="minus" data-cart-id="' . $row['cart_id'] . '">-</button>';
                            echo '<input type="number" name="quantity" value="' . $row['quantity'] . '" min="1" class="quantity-input">';
                            echo '<button type="button" class="quantity-change" data-change="plus" data-cart-id="' . $row['cart_id'] . '">+</button></p>';
                            echo '</div>';
                            echo '</form>';
                            echo '<form class="item-remove-form" action="" method="post">';
                            echo '<div class="cart-item-remove">';
                            echo '<input type="hidden" name="cart_id" value="' . $row['cart_id'] . '">';
                            echo '<button type="submit" name="remove_item" onclick="return confirmRemove()">Remove</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</form>';
                            $total_amount += $row['item_price'] * $row['quantity'];
                        }
                    } else {
                        echo '<div class= "empty">';
                        echo '<img src="../ASSETS/icon4.png" alt="Empty Cart Icon" class="empty-cart-icon">';
                        echo '<p>Your cart is empty.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
                </div>
                
                <div class="order-summary">
                    <div class="total">
                    <?php
                        echo '<div class="total-amount">';
                        echo '<p>Total Amount Due </p>';
                        echo '<p class="price">₱' . $total_amount . '</p>';
                        echo '</div>';
                    ?>
                    </div>
                    <div class="order-form">
                    <?php
                    $_SESSION['total_amount'] = $total_amount;
                    $_SESSION['user_id'] = $userid;
                    ?>
                        <form method="POST" action="">
                        <button type="submit" name="place_order" <?php if (!$hasCartItems) 
                        echo 'disabled style="background-color: darkgray;"'; ?>>Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
            <footer class="site-footer">
                <p>&copy; 2023 WIBS. All rights reserved.</p>
            </footer>
            <script src="../js/cart.js"></script>
        </body>
        </html>