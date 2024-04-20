<?php
include '../database/connect.php';
session_start(); // Make sure to start the session to access user session variables

// Check if the form was submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product codes and quantities from the form data
    $productCodes = $_POST["product-code"];
    $quantities = $_POST["quantity"];

    // Loop through each product code and quantity pair
    foreach ($productCodes as $key => $product_code) {
        $quantity = $quantities[$key];
        // Update the quantity in the database for each product code
        $sql = "UPDATE `data` SET quantity = quantity + ? WHERE product_code = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            mysqli_stmt_bind_param($stmt, "is", $quantity, $product_code);

            if (mysqli_stmt_execute($stmt)) {
                // Quantity updated successfully
            } else {
                echo "Error updating record: " . mysqli_error($con);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($con);
        }
    }

    // Log the supply in submission in the activity logs
    $user_id = $_SESSION['user_id'] ?? 0;
    $action = "Submitted in the Supply In";
    $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES (?, ?)";

    if ($log_stmt = mysqli_prepare($con, $log_sql)) {
        mysqli_stmt_bind_param($log_stmt, "is", $user_id, $action);
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);
    }

    echo "Quantity updated successfully"; // Send a response back to the client
}

// Close the connection
mysqli_close($con);

?>
