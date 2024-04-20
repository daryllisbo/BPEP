<?php
include '../database/connect.php';

if(isset($_POST['product-code']) && isset($_POST['new-price'])){
    $product_code = $_POST['product-code'];
    $new_price = $_POST['new-price'];

    // Prepare an update statement
    $sql = "UPDATE `data` SET unit_price=? WHERE product_code=?";
    $stmt = mysqli_prepare($con, $sql);

    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ds", $new_price, $product_code);

    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        echo "Price updated successfully.";
        // Redirect back to the inventory page or wherever appropriate
        header('Location: index.php');
    } else{
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($con);
    }
}
?>
