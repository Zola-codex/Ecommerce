<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: deliverylogin.php");
    exit;
}

include("../dbbcon.php");

if (isset($_POST['delivered'])) {
    $c_id = $_POST['c_id'];

    // Use prepared statements to prevent SQL injection
    $deleteQuery = "DELETE FROM delivery WHERE c_id = ?";
    $deleteStatement = mysqli_prepare($dbcon, $deleteQuery);
    mysqli_stmt_bind_param($deleteStatement, 'i', $c_id);
    mysqli_stmt_execute($deleteStatement);
    mysqli_stmt_close($deleteStatement);

    // Assume you have obtained the report message from some process or logic
    $reportMessage = "Products for customer ID $c_id have been sold out and delivered successfully.";

    // Use prepared statements to prevent SQL injection
    $insertQuery = "INSERT INTO delivery_reports (c_id, report_message) VALUES (?, ?)";
    $insertStatement = mysqli_prepare($dbcon, $insertQuery);
    mysqli_stmt_bind_param($insertStatement, 'is', $c_id, $reportMessage);
    mysqli_stmt_execute($insertStatement);
    mysqli_stmt_close($insertStatement);

    // Use JavaScript to display an alert with the report message
    echo '<script>alert("' . $reportMessage . '");</script>';

    exit();
}

$query = "SELECT c.*, d.deli_person
          FROM customer c
          INNER JOIN delivery d ON c.c_id = d.c_id";

$result = mysqli_query($dbcon, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title> Delivery | YOHANIS SPAREPART STORE</title>

    <link href="../css/style.css" rel="stylesheet" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        button {
            padding: 12px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .customer-info {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        .assign-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4caf50;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .assigned-text {
            background-color: red;
            color: white;
            width: 80px;
            padding: 5px;
            border-radius: 4px;
        }

        
    </style>

</head>

<body>
    <div class="hero_area">
        <header class="header_section">
            <nav class="navbar navbar-expand-lg custom_nav-container ">
                <div class="collapse navbar-collapse innerpage_navbar" id="navbarSupportedContent">
                    <ul class="navbar-nav  ">
                        <h6 style="padding-right: 45px; font-size: 22px; font-family: Arial, Helvetica, sans-serif;">YOHANNIS SPARES</h6>
                        <li class="nav-item active">
                            <a class="nav-link" href="delivery.php">
                                Delivery orders
                            </a>
                        </li>
                        
                        <div class="user_option">
                            <a href="deliverylogin.php">
                                <i class="bi bi-person"></i>
                                <span>Login</span>
                            </a>
                        </div>
                    </ul>
                </div>
            </nav>
            <br><br><br>

            <?php
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="customer-info">';
        echo '<h2>Customer Information</h2>';
        echo '<p>Customer ID: ' . $row['c_id'] . '</p>';
        echo '<p>Name: ' . $row['name'] . '</p>';
        echo '<p>Location: ' . $row['address'] . '</p>';
        echo '<p>Phone: ' . $row['phone_no'] . '</p>';
        echo '<p>Delivery Type: ' . $row['deli_type'] . '</p>';
        echo '<p>Assigned Delivery Person: ' . $row['deli_person'] . '</p>';

        // Add "I Delivered" button with a form for each customer
        echo '<form method="post" action="delivery.php">';
        echo '<input type="hidden" name="c_id" value="' . $row['c_id'] . '">';
        echo '<input type="submit" name="delivered" value="I Delivered">';
        echo '</form>';

        echo '</div>';
    }
} else {
    echo "Error fetching data: " . mysqli_error($dbcon);
}

mysqli_close($dbcon);
?>


            
        </header><br><br>
    </div>
</body>

</html>
