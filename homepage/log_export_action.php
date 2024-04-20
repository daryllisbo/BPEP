<?php
include '../database/connect.php';
session_start();

if (isset($_POST['action']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get user ID from session
    $action = mysqli_real_escape_string($con, $_POST['action']);

    $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES ('$user_id', '$action')";
    if (mysqli_query($con, $log_sql)) {
        echo "Log entry added successfully.";
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    echo "No data to log or user is not logged in.";
}
?>
