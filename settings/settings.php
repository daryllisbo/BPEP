<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="home_settings.css">
</head>
<body>
    <div class="setting_sidebar">
        <div class="setting_logo-container">
            <img src="../img/bpep_white.png" alt="BPEP Logo">
            <div class="title">Business Partner Enterprise Philippines Inc.</div>
        </div>
        <ul>
            <li><a href="../homepage/index.php"><i class="fas fa-box"></i> Inventory</a></li>
            <li><a href="../supply_in/index.php"><i class="fas fa-truck"></i> Supply In</a></li>
            <li><a href="../product_out/index.php"><i class="fas fa-dolly"></i> Product Out</a></li>
            <li><a href="../create_account/index.php"><i class="fas fa-user-plus create-icon"></i> Create Account</a></li>
            <li><a href="../activity_logs/index.php"><i class="fas fa-list-alt"></i> Activity Logs</a></li>
            <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Log out</a></li>
        </ul>
        <!-- Settings button -->
        <div class="settings-link">
            <a href="../settings/settings.php"><i class="fas fa-cog"></i> Settings</a>
        </div>
        <div class="user-info">
            <?php
            session_start(); // Start the session
            if (isset($_SESSION['firstName'])) {
                $firstName = $_SESSION['firstName'];
                echo "Welcome, $firstName!";
            } else {
                // Handle case where user is not logged in
            }
            ?>
        </div>
    </div>
    <div class="content">
    <h1 class="settings-title">Settings</h1>
    <div class="settings-container">
    <form method="POST" action="download_template.php">
    <div class="form-group file-input-container">
        <label for="excelFile">Import Data from Excel file:</label>
        <div class="file-input-wrapper">
            <input type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls" />
        </div>
        <div class="button-wrapper">
            <button type="submit" class="download-button" name="downloadTemplateBtn">Download Template</button>
            <button type="submit" class="import-button" name="importExcelBtn">Import Excel File</button>
        </div>
    </div>
</form>
    </div>
</div>

</body>
</html>
