<?php
include '../database/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'] ?? 0; // Default to 0 if no session
    $description = $_POST['description'] ?? 'Exported PDF'; // Default description

    $sql = "INSERT INTO activity_logs (user_id, action) VALUES (?, ?)";
    if ($stmt = $con->prepare($sql)) {
        mysqli_stmt_bind_param($stmt, "is", $user_id, $description);
        mysqli_stmt_execute($stmt);
        echo "Log recorded successfully";
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($con);
    }
    mysqli_close($con);
} else {
    echo "Invalid request method or parameters";
}
?>
