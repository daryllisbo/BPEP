<?php
// Assuming you're using the same database connection setup
include '../database/connect.php';

$product_code = $_GET['product_code'] ?? ''; // Get the product code from the request

if ($product_code !== "") {
    $query = mysqli_query($con, "SELECT model_description, brand FROM data WHERE product_code = '$product_code'");
    $row = mysqli_fetch_array($query);
    if ($row) {
        $response = [
            'model_description' => $row["model_description"],
            'brand' => $row["brand"]
        ];
        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }
} else {
    // Return a default response if the product code is empty
    echo json_encode(["error" => "Product code is empty"]);
}
?>

