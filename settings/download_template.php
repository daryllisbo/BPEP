<?php
require '../vendor/autoload.php'; // Adjust the path to point to your 'vendor' directory
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create the temp directory if it doesn't exist
$tempDir = '../temp/';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true); // Create the directory with full permissions
}

// Create a new PhpSpreadsheet instance
$spreadsheet = new Spreadsheet();

// Set the column headers
$spreadsheet->getActiveSheet()
    ->fromArray(
        ['product_code', 'brand', 'model_description', 'unit_price', 'current_quantity'],
        NULL,
        'A1'
    );

// Create a writer object and set the output file path
$writer = new Xlsx($spreadsheet);
$fileName = 'template.xlsx';
$outputFilePath = $tempDir . $fileName;

// Save the Excel file to the specified path
$writer->save($outputFilePath);

// Force download the generated Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Content-Length: ' . filesize($outputFilePath));
readfile($outputFilePath);

// Delete the temporary Excel file after download
unlink($outputFilePath);
exit();
?>
