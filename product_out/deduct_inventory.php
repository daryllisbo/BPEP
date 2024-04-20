<?php
include '../database/connect.php';
session_start();  // Start the session to access user information

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product codes and quantities from the form data (assuming they are arrays)
    $productCodes = $_POST['product-code'];
    $quantitiesToDeduct = $_POST['quantity'];

    // Loop through each product code and quantity pair for deduction
    foreach ($productCodes as $key => $product_code) {
        $quantity_to_deduct = $quantitiesToDeduct[$key]; // Get the quantity to deduct

        // Update the new quantity in the database for each product code
        $sql_update = "UPDATE data SET quantity = quantity - ? WHERE product_code = ?";
        if ($stmt_update = $con->prepare($sql_update)) {
            $stmt_update->bind_param("is", $quantity_to_deduct, $product_code);
            $stmt_update->execute();

            if ($stmt_update->affected_rows > 0) {
                echo "Quantity updated successfully";

                // Log the activity including the quantity deduction
                $user_id = $_SESSION['user_id'] ?? 0; // Get the user ID from session
                $po_number = $_POST['po-number'][$key] ?? 'N/A'; // Get the PO number for logging
                $supplier_name = $_POST['supplier-name'][$key] ?? 'N/A'; // Get the supplier name for logging
                $action = "Submitted in Product Out: PO# $po_number, Supplier: $supplier_name, Quantity Deducted: $quantity_to_deduct";
                $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES (?, ?)";
                if ($log_stmt = $con->prepare($log_sql)) {
                    $log_stmt->bind_param("is", $user_id, $action);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            } else {
                echo "Error updating quantity";
            }
            $stmt_update->close();
        } else {
            echo "Error preparing statement: " . $con->error;
        }
    }
} else {
    echo "Invalid request method";
}

$con->close();
?>
