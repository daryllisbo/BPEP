<?php
session_start(); // Start the session
include '../database/connect.php';

$sql = "SELECT al.id, al.user_id, al.action, al.timestamp, r.firstName 
        FROM activity_logs al 
        INNER JOIN registration r ON al.user_id = r.id 
        ORDER BY al.timestamp DESC";
$result = mysqli_query($con, $sql);

// Export to Excel handler
if (isset($_POST['export_to_excel'])) {
    // Tell the browser it's going to be a CSV file
    header('Content-Type: text/csv; charset=utf-8');
    // Tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename=activity_logs.csv');
    
    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    
    // Output the column headings
    fputcsv($output, array('Timestamp', 'User Name', 'Remarks'));
    
    // Fetch the data from the database
    $sql = "SELECT al.timestamp, r.firstName AS user_name, al.action AS remarks
            FROM activity_logs al
            INNER JOIN registration r ON al.user_id = r.id
            ORDER BY al.timestamp DESC";
    $rows = mysqli_query($con, $sql);
    
    // Loop over the rows, outputting them directly to the output stream
    while ($row = mysqli_fetch_assoc($rows)) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="logs.css"> <!-- Make sure this path is correct -->
</head>
<body>

<div class="log_sidebar">
        <div class="log_logo-container">
            <img src="../img/bpep_white.png" alt="BPEP Logo">
            <div class="title">Business Partner Enterprise Philippines Inc.</div>
        </div>
        <ul>
            <li><a href="../homepage/index.php"><i class="fas fa-box"></i> Inventory</a></li>
            <li><a href="../supply_in/index.php"><i class="fas fa-truck"></i> Supply In</a></li>
            <li><a href="../product_out/index.php"><i class="fas fa-dolly"></i> Product Out</a></li>
            <li><a href="../create_account/index.php"><i class="fas fa-user-plus create-icon"></i> Create Account</a>
            </li>
            <li><a href="../activity_logs/index.php"><i class="fas fa-list-alt"></i> Activity Logs</a></li>
            <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Log out</a></li>
        </ul>

        <!-- Settings button -->
        <div class="log_settings-link">
            <a href="../settings/settings.php"><i class="fas fa-cog"></i> Settings</a>
        </div>

        <div class="log_user-info">
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['firstName'])) {
                $firstName = $_SESSION['firstName'];
                echo "Welcome, $firstName!";
            } else {
                // Handle case where user is not logged in
            }
            ?>
        </div>

    </div>

    <div class="main-content">
    <div class="header-container">
    <h1>Activity Logs</h1>
    <div class="export-btn">
        <form method="post">
            <button type="submit" name="export_to_excel" id="exportBtn">Export to Excel</button>
        </form>
    </div>
</div>
<div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search...">
            <button onclick="searchItems()">Search</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User Name</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['timestamp']; ?></td>
        <td><?php echo $row['firstName']; ?></td> <!-- Display the user's first name -->
        <td><?php echo $row['action']; ?></td>
    </tr>
<?php endwhile; ?>

            </tbody>
        </table>
    </div>

    <script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tr');
        const csv = [];
        
        // Add table headers to CSV
        const headerRow = [];
        table.querySelectorAll('th').forEach(function(headerCell) {
            headerRow.push(headerCell.innerText);
        });
        csv.push(headerRow.join(','));

        // Add table rows to CSV
        rows.forEach(function(row) {
            const dataRow = [];
            row.querySelectorAll('td').forEach(function(cell) {
                dataRow.push(cell.innerText);
            });
            csv.push(dataRow.join(','));
        });

        // Create CSV file and initiate download
        const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'activity_logs.csv');
        document.body.appendChild(link);
        link.click();
    });

    document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.querySelector('.search-bar input[type="text"]');
            const rows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                rows.forEach(function (row) {
                    const cells = row.querySelectorAll('td');
                    let found = false;

                    cells.forEach(function (cell) {
                        if (cell.textContent.toLowerCase().includes(searchTerm)) {
                            found = true;
                        }
                    });

                    if (found) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Reset pagination to page 1 when search input is cleared
                if (searchTerm === '') {
                    currentPage = 1;
                    updateTableVisibility();
                    updatePagerButtons();
                }
            });
        });
</script>



</body>
</html>