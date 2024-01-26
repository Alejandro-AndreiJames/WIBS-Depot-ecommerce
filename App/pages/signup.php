<?php
include 'db_conn.php';
$error = array();

if (isset($_POST['submit'])) {

  $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
  $name = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $pass = md5($_POST['password']);
  $cpass = md5($_POST['confirm_password']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $mobile_num = mysqli_real_escape_string($conn, $_POST['mobile_num']);
  $vrzn_num = mysqli_real_escape_string($conn, $_POST['vrzn_num']);
  $apex_num = mysqli_real_escape_string($conn, $_POST['apex_num']);

  $select = "SELECT * FROM customer WHERE email = '$email'";

  $result = mysqli_query($conn, $select);

  if (mysqli_num_rows($result) > 0) {
    $error[] = 'User already exists!';
  } else {
    if ($pass != $cpass) {
      $error[] = 'Passwords do not match!';
    } else {
      $insert = "INSERT INTO customer(firstname, lastname, name, email, password, address, mobile_num, vrzn_num, apex_num) VALUES('$firstname', '$lastname', '$name', '$email', '$pass', '$address', '$mobile_num', '$vrzn_num', '$apex_num')";
      mysqli_query($conn, $insert);
      header('location:login.php');
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="../ASSETS/wd.png" />
  <link rel="stylesheet" href="../css/signup_styles.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter">
  <title>WIBS Sign Up</title>
</head>

<body>
  <header class="header">
    <div></div>
    <div class="header-title"><img src="../ASSETS/logo.png" alt="pciture"></div>
  </header>

  <div class="content">
    <div class="content-image">
      <img src="../ASSETS/Man presenting business idea on laptop.png" alt="">
    </div>

    <div class="content-form">
      <form id="signup-form" method="post">
        <h1>Sign Up</h1>

        <!-- Error Message Container -->
        <div class="error-container">
          <?php
            if (!empty($error)) {
            foreach ($error as $err) {
            echo '<span class="error-msg">' . $err . '</span>';
            }
            }
          ?>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" class="input-width" id="firstname" name="firstname" placeholder="Input First Name" required/>
          </div>

          <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname" placeholder="Input Last Name" required/>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Input Username" required/>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" placeholder="Input Email" required/>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" placeholder="Input Password" required/>
              <div class="toggle-password" onclick="togglePasswordVisibility('password', 'eye-icon1')">
                  <img id="eye-icon1" src="../ASSETS/close.png" alt="Toggle Password Visibility" />
              </div>
          </div>

          <div class="form-group">
              <label for="confirm_password">Confirm Password</label>
              <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required/>
              <div class="toggle-password" onclick="togglePasswordVisibility('confirm_password', 'eye-icon2')">
                  <img id="eye-icon2" src="../ASSETS/close.png" alt="Toggle Password Visibility" />
              </div>
          </div>
      </div>

        <div class="form-row">
          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" placeholder="Input Address" required/>
          </div>

          <div class="form-group">
            <label for="mobile_num">Mobile Number</label>
            <input type="number" id="mobile_num" name="mobile_num" placeholder="Input Number" required/>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="vrzn_num">VRZN Bank</label>
            <input type="number" id="vrzn_num" name="vrzn_num" placeholder="Input VRZN Bank Account Number (Optional)" />
          </div>

          <div class="form-group">
            <label for="apex_num">APEX Bank</label>
            <input type="number" id="apex_num" name="apex_num" placeholder="Input VRZN Bank Account Number (Optional)" />
          </div>
        </div>

        <div class="form-group">
          <button id="signup" type="submit" name="submit">Sign Up</button>
        </div>
        <div id="signupResult"></div>
        <div class="signup-link">
          Already have an account? <a href="login.php">Sign In</a>.
        </div>
      </form>
    </div>
  </div>
  <footer>
    <img src="../ASSETS/Blue shopping bag in air.png" alt="">
  </footer>
</body>
<script src="../js/signup.js"></script>

</html>