<?php
            #$conn = mysqli_connect('127.0.0.1:3306','u733671518_wibs','|4Kh/3XYD','u733671518_project');
            $conn = mysqli_connect('localhost','root','','u733671518_project');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

?>  