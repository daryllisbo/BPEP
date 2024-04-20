<?php
include '../database/connect.php';
require '../vendor/autoload.php'; // Adjust the path to point to your 'vendor' directory
use PhpOffice\PhpSpreadsheet\IOFactory;

session_start(); // Ensure session is started at the top of the script

if (isset($_POST['importExcelBtn']) && isset($_FILES['excelFile'])) {
    $file = $_FILES['excelFile'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileParts = explode('.', $fileName);
    $fileExtension = strtolower(end($fileParts));

    if ($fileExtension !== 'xlsx' && $fileExtension !== 'xls') {
        echo "Please upload an Excel file.";
        exit();
    }

    // Use PhpSpreadsheet's IOFactory to load the Excel file
    $spreadsheet = IOFactory::load($fileTmpName);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    $successfulInserts = 0; // Counter for successful inserts

    // Skip the first row (header row) by starting the loop from index 1
    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];
        $product_code = mysqli_real_escape_string($con, $row[0]);
        $brand = mysqli_real_escape_string($con, $row[1]);
        $model_description = mysqli_real_escape_string($con, $row[2]);
        $unit_price = mysqli_real_escape_string($con, $row[3]);
        $quantity = mysqli_real_escape_string($con, $row[4]);

        $sql = "INSERT INTO `data` (product_code, brand, model_description, unit_price, quantity) VALUES ('$product_code', '$brand', '$model_description', '$unit_price', '$quantity')";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $successfulInserts++;
        } else {
            echo "Error on row $i: " . mysqli_error($con);
        }
    }

    // Log the activity
    if ($successfulInserts > 0) {
        $user_id = $_SESSION['user_id']; // Get user ID from session
        $action = "Imported $successfulInserts items from Excel file.";
        $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES ('$user_id', '$action')";
        $log_result = mysqli_query($con, $log_sql);
        if (!$log_result) {
            echo "Failed to log activity: " . mysqli_error($con);
        } else {
            // JavaScript alert and redirection
            echo "<script>alert('Data imported successfully.');</script>";
            echo "<script>window.location.href = '../homepage/index.php';</script>";
        }
    }
}
?>
