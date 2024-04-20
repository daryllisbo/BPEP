<?php
include '../database/connect.php';
session_start();

if (isset($_POST['action']) && $_POST['action'] == "exported_to_pdf") {
    $user_id = $_SESSION['user_id'] ?? 0; // Default to 0 if no session
    $description = $_POST['description']; // Get the description from POST data

    $sql = "INSERT INTO activity_logs (user_id, action) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "is", $user_id, $description);
        if (mysqli_stmt_execute($stmt)) {
            echo "Log recorded successfully";
        } else {
            echo "Failed to record log: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($con);
    }
    mysqli_close($con);
}
?>
