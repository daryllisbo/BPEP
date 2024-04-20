<?php
include '../database/connect.php';

if (isset($_POST['submit'])) {
    // Sanitize the POST data to prevent SQL Injection
    $product_code = mysqli_real_escape_string($con, $_POST["product-code"]);
    $model_description = mysqli_real_escape_string($con, $_POST["model-description"]);
    $unit_price = mysqli_real_escape_string($con, $_POST["unit-price"]);
    $brand = mysqli_real_escape_string($con, $_POST["brand"]);

    // Insert new product
    $sql = "INSERT INTO `data` (product_code, model_description, unit_price, brand) VALUES ('$product_code', '$model_description', '$unit_price', '$brand')";
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo "Data inserted successfully";
        session_start(); // Start the session
        $user_id = $_SESSION['user_id']; // Get user ID from session
        $firstName = $_SESSION['firstName']; // Get user's first name from session

        // Log the action of adding a new product
        $action = "Added new product: " . $product_code;
        $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES ('$user_id', '$action')";
        $log_result = mysqli_query($con, $log_sql);
        if (!$log_result) {
            // If there's an error logging the action, handle it here
            echo "Failed to log activity.";
        }
    } else {
        die(mysqli_error($con)); // Error handling for product insertion
    }
}

if (isset($_POST['updateUnitPrice'])) {
    $productCode = mysqli_real_escape_string($con, $_POST['productCode']);
    $newUnitPrice = mysqli_real_escape_string($con, $_POST['unitPrice']);

    $updateSql = "UPDATE `data` SET unit_price = '$newUnitPrice' WHERE product_code = '$productCode'";
    $updateResult = mysqli_query($con, $updateSql);

    if ($updateResult) {
        echo "Unit price updated successfully";
    } else {
        echo "Error updating unit price: " . mysqli_error($con);
    }
}

if (isset($_POST['updateUnitPrice'])) {
    $productCode = mysqli_real_escape_string($con, $_POST['productCode']);
    $newUnitPrice = mysqli_real_escape_string($con, $_POST['unitPrice']);

    $updateSql = "UPDATE `data` SET unit_price = '$newUnitPrice' WHERE product_code = '$productCode'";
    $updateResult = mysqli_query($con, $updateSql);

    if ($updateResult) {
        echo "Unit price updated successfully";
        // Fetch the model description for logging
        $descSql = "SELECT model_description FROM `data` WHERE product_code = '$productCode'";
        $descResult = mysqli_query($con, $descSql);
        $row = mysqli_fetch_assoc($descResult);
        $modelDescription = $row['model_description'];

        // Log the action of updating the product price
        session_start(); // Ensure session is started
        $user_id = $_SESSION['user_id']; // Get user ID from session
        $action = "Updated price for product: $productCode, Model: $modelDescription to ₱$newUnitPrice";
        $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES ('$user_id', '$action')";
        $log_result = mysqli_query($con, $log_sql);
        if (!$log_result) {
            echo "Failed to log activity: " . mysqli_error($con);
        }
    } else {
        echo "Error updating unit price: " . mysqli_error($con);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="sidebar">
        <div class="logo-container">
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
        <div class="settings-link">
            <a href="../settings/settings.php"><i class="fas fa-cog"></i> Settings</a>
        </div>

        <div class="user-info">
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

    <div class="container">
        <div class="inventory-header">
            <h1><i class="fas fa-box"></i> Inventory</h1>
        </div>
        <button id="addItemButton" class="add-item-button">Add Item</button> <!-- New Add Item button -->
        <button id="exportExcelButton" class="exporty" onclick="generateExcel()">Export Excel</button>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search...">
            <button onclick="searchItems()">Search</button>
        </div>


        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th> <!-- "#" column added -->
                        <th>Product Code</th>
                        <th>Brand</th>
                        <th>Model/Description
                            <button id="modelSort" class="sort-button" onclick="toggleSort(3)">
                                <i class="fas fa-sort"></i>
                            </button>
                        </th>
                        <th>Unit Price (₱)
                            <button id="priceSort" class="sort-button" onclick="toggleSort(4)">
                                <!-- Updated column index to 3 -->
                                <i class="fas fa-sort"></i>
                            </button>
                        </th>
                        <th>Current Quantity
                            <button id="quantitySort" class="sort-button" onclick="toggleSort(5)">
                                <!-- Updated column index to 4 -->
                                <i class="fas fa-sort"></i>
                            </button>
                        </th>
                        <th>Total Price
                            <button id="totalPriceSort" class="sort-button" onclick="toggleSort(6)">
                                <i class="fas fa-sort"></i>
                            </button>
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $count = 1;

                    $sql = "Select * from `data`";
                    $result = mysqli_query($con, $sql);
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $product_code = $row['product_code'];
                            $brand = $row['brand'];
                            if ($brand === null) {
                                $brand = "Not set"; // Or any placeholder you want to show when brand is null.
                            }
                            $model_description = $row['model_description'];
                            $unit_price = number_format(str_replace(',', '', $row['unit_price']), 2);
                            $current_quantity = $row['quantity'];
                            $total_price = number_format(str_replace(',', '', $row['unit_price']) * $current_quantity, 2);
                            echo ' <tr>
                        <td>' . $count++ . '</td> <!-- Add the "#" column -->
                        <td><strong>' . $product_code . '</strong></td>
                        <td>' . $brand . '</td>
                        <td>' . $model_description . '</td>
                        <td>' . $unit_price . '</td>
                        <td>' . $current_quantity . '</td>
                        <td>' . $total_price . '</td>
                        <td><button class="view-details-btn" onclick="updateItem(this)">Update</button></td>

                    </tr>';
                        }
                    }

                    ?>

                </tbody>
            </table>
        </div>

        <!-- Pager section -->
        <div class="pager-container">
            <ul class="pager">
                <li><a href="#" class="prev">&laquo; Prev</a></li>
                <li><a href="#" class="page active">1</a></li>
                <li><a href="#" class="page">2</a></li>
                <li><a href="#" class="page">3</a></li>
                <li><span class="ellipsis">...</span></li>
                <li><a href="#" class="page">10</a></li>
                <li><a href="#" class="next">Next &raquo;</a></li>
                <div id="totalEntries" class="total-entries">Total entries: </div>
            </ul>
        </div>
    </div>

    <!-- Modal for adding new item -->
    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Item</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="product-code" class="addtitle">Product Code <span class="required">*</span> </label>
                    <input type="text" id="product-code" name="product-code" placeholder="Enter product code" required>
                </div>
                <div class="form-group">
                    <label for="brand" class="addtitle">Brand <span class="required">*</span></label>
                    <input type="text" id="brand" name="brand" placeholder="Enter brand" required>
                </div>

                <div class="form-group">
                    <label for="model-description" class="addtitle">Model/Description <span class="required">*</span></label>
                    <input type="text" id="model-description" name="model-description"
                        placeholder="Enter model/description" required>
                </div>
                <div class="form-group">
                    <label for="unit-price" class="addtitle">Unit Price (₱) <span class="required">*</span></label>
                    <input type="text" id="unit-price" name="unit-price" placeholder="Enter unit price" required>
                </div>
                <button type="submit" class="btn" name="submit">Add Item</button>
            </form>
        </div>
    </div>

    <!-- Modal for updating price -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Unit Price</h2>
            <form id="updatePriceForm" method="POST">
                <div class="form-group">
                    <label for="productCode">Product Code:</label>
                    <input type="text" id="productCode" name="productCode" readonly>
                </div>
                <div class="form-group">
                    <label for="currentQuantity">Current Quantity:</label>
                    <input type="text" id="currentQuantity" name="currentQuantity" readonly>
                </div>
                <div class="form-group">
                    <label for="unitPrice">New Unit Price (₱):</label>
                    <input type="text" id="unitPrice" name="unitPrice" required>
                </div>
                <button type="submit" class="btn" name="updateUnitPrice">Update Unit Price</button>
            </form>
        </div>
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.2/xlsx.full.min.js"></script>
    <script>
        let sortOrder = ''; // Global variable to store the current sort order
        let currentColumnIndex = 3; // Default column index for sorting is Product Code

        function updateItem(button) {
            var row = button.closest('tr');
            var productCode = row.cells[1].innerText;
            var currentQuantity = row.cells[5].innerText;
            var unitPrice = row.cells[4].innerText.replace('₱', '').replace(',', '');

            // Populate modal with data
            document.getElementById('productCode').value = productCode;
            document.getElementById('currentQuantity').value = currentQuantity;
            document.getElementById('unitPrice').value = unitPrice;

            // Show modal
            document.getElementById('updateModal').style.display = 'block';

            // Close modal when clicking on close button
            document.getElementsByClassName('close')[1].addEventListener('click', function () {
                document.getElementById('updateModal').style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.onclick = function (event) {
                var modal = document.getElementById('updateModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }




        // Function to update total entries count
        function updateTotalEntriesCount() {
            const totalEntriesElement = document.getElementById('totalEntries');
            const totalEntries = document.querySelectorAll('tbody tr').length;
            totalEntriesElement.textContent = 'Total entries: ' + totalEntries;
        }

        // Update total entries count after successful insertion
        updateTotalEntriesCount();

        function toggleSort(columnIndex) {
            const button = document.getElementById(getSortButtonId(columnIndex));
            const icon = button.querySelector('i');

            if (currentColumnIndex === columnIndex) {
                if (sortOrder === '') {
                    sortOrder = 'asc';
                    button.classList.add('active', 'asc');
                } else if (sortOrder === 'asc') {
                    sortOrder = 'desc';
                    button.classList.remove('asc');
                } else {
                    sortOrder = 'asc';
                    button.classList.add('asc');
                }
            } else {
                // Reset previous column sorting
                const prevButton = document.getElementById(getSortButtonId(currentColumnIndex));
                prevButton.classList.remove('active', 'asc');
                prevButton.querySelector('i').classList.remove('fa-sort-up', 'fa-sort-down');

                // Set new column sorting
                sortOrder = 'asc';
                button.classList.add('active', 'asc');
                icon.classList.add('fa-sort-up');
                currentColumnIndex = columnIndex;
            }

            // Toggle arrow icon
            icon.classList.remove('fa-sort-up', 'fa-sort-down');
            if (sortOrder === 'asc') {
                icon.classList.add('fa-sort-up');
            } else {
                icon.classList.add('fa-sort-down');
            }

            // Call sorting function
            sortTable(columnIndex, sortOrder);

            // Update visibility of table rows based on current page after sorting
            updateTableVisibility();
        }

        function sortTable(columnIndex, order) {
            const table = document.querySelector('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                const x = a.cells[columnIndex].innerText.trim();
                const y = b.cells[columnIndex].innerText.trim();

                if (columnIndex === 4 || columnIndex === 5 || columnIndex === 6) { // Numeric sorting
                    const xValue = parseFloat(x.replace(',', ''));
                    const yValue = parseFloat(y.replace(',', ''));
                    return order === 'asc' ? xValue - yValue : yValue - xValue;
                } else { // String sorting
                    return order === 'asc' ? x.localeCompare(y) : y.localeCompare(x);
                }
            });

            // Clear existing rows
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }

            // Reinsert sorted rows into the table
            rows.forEach(row => tbody.appendChild(row));
        }

        function getSortButtonId(columnIndex) {
            switch (columnIndex) {
                case 3:
                    return 'modelSort';
                case 4:
                    return 'quantitySort';
                case 5:
                    return 'priceSort';
                case 6:
                    return 'totalPriceSort';
                default:
                    return '';
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const links = document.querySelectorAll('.sidebar ul li a');

            links.forEach(function (link) {
                link.addEventListener('click', function () {
                    // Remove active class from all links
                    links.forEach(function (item) {
                        item.classList.remove('active');
                    });

                    // Add active class to the clicked link
                    this.classList.add('active');
                });
            });

            // Highlight current active link based on current URL
            const currentUrl = window.location.href;

            links.forEach(function (link) {
                if (link.href === currentUrl) {
                    link.classList.add('active');
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const logoutLink = document.querySelector('.sidebar ul li a[href="../index.html"]');

            logoutLink.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent the default action of navigating to the logout page

                const confirmation = confirm("Are you sure you want to log out?");
                if (confirmation) {
                    // Proceed with logout by navigating to the logout page
                    window.location.href = logoutLink.href;
                }
            });
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

        document.addEventListener("DOMContentLoaded", function () {
            const addItemButton = document.getElementById('addItemButton');
            const addItemModal = document.getElementById('addItemModal');
            const closeBtn = document.querySelector('#addItemModal .close');

            addItemButton.addEventListener('click', function () {
                addItemModal.style.display = 'block';
            });

            closeBtn.addEventListener('click', function () {
                addItemModal.style.display = 'none';
            });

            window.onclick = function (event) {
                if (event.target == addItemModal) {
                    addItemModal.style.display = 'none';
                }
            };

            const addItemForm = document.getElementById('addItemForm');
            addItemForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent form submission for demonstration

                // Here you can add logic to handle form submission
                // For demonstration purposes, let's just close the modal
                addItemModal.style.display = 'none';
            });
        });

        // Function to generate Excel
        function generateExcel() {
    // Temporarily show all rows for export
    const allRows = document.querySelectorAll('table tbody tr');
    allRows.forEach(row => {
        row.style.display = '';
    });

    // Export to Excel
    const wb = XLSX.utils.table_to_book(document.querySelector('table'), {
        sheet: "Sheet JS"
    });
    XLSX.writeFile(wb, 'inventory.xlsx');

    // Log the action to the activity log
    logExportAction();

    // Reapply pagination after export
    updateTableVisibility();
}

function logExportAction() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "log_export_action.php", true); // Adjust the URL to the correct PHP script path
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            console.log("Export logged: " + this.responseText); // Optionally display a message or handle errors
        }
    };
    xhr.send("action=Exported data to Excel");
}

        // Function to delete a row
        function deleteRow(row) {
            row.parentNode.removeChild(row);

            // Update the numbering after deletion
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.cells[0].innerText = index + 1;
            });
        }

        // Function to update visibility of table rows based on current page
        function updateTableVisibility() {
            const tableRows = document.querySelectorAll('tbody tr');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            tableRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Function to handle pagination button clicks
        function handlePaginationClick(event) {
            event.preventDefault();

            if (this.classList.contains('prev')) {
                currentPage = Math.max(1, currentPage - 1);
            } else if (this.classList.contains('next')) {
                const totalPages = Math.ceil(document.querySelectorAll('tbody tr').length / itemsPerPage);
                currentPage = Math.min(totalPages, currentPage + 1);
            } else if (this.classList.contains('page')) {
                currentPage = parseInt(this.textContent);
            }

            updateTableVisibility();
            updatePagerButtons();
        }

        // Function to update the state of pager buttons
        function updatePagerButtons() {
            const totalPages = Math.ceil(document.querySelectorAll('tbody tr').length / itemsPerPage);
            const pager = document.querySelector('.pager');

            // Remove only the numeric page buttons
            const oldPageNumbers = pager.querySelectorAll('.page:not(.prev):not(.next)');
            oldPageNumbers.forEach(pageNumber => pageNumber.remove());

            // Calculate the range of pages to display
            let startPage = Math.max(currentPage - 2, 1);
            let endPage = Math.min(startPage + 4, totalPages);
            startPage = Math.max(endPage - 4, 1); // Adjust start page if end page changes

            // Insert the page number elements
            for (let i = startPage; i <= endPage; i++) {
                const pageLink = document.createElement('a');
                pageLink.href = '#';
                pageLink.textContent = i;
                pageLink.className = 'page' + (i === currentPage ? ' active' : '');
                pageLink.addEventListener('click', handlePaginationClick);
                nextButton.parentNode.insertBefore(pageLink, nextButton); // Insert before the "Next" button
            }

            // Update the state of the "Prev" and "Next" buttons
            prevButton.classList.toggle('disabled', currentPage === 1);
            nextButton.classList.toggle('disabled', currentPage === totalPages);

            // Hide the ellipsis if we're at the beginning or end of the list
            const ellipsis = pager.querySelector('.ellipsis');
            if (ellipsis) {
                ellipsis.style.display = (startPage > 1 && endPage < totalPages) ? 'inline' : 'none';
            }
        }

        // Call the updatePagerButtons function to initialize the pager on page load
        document.addEventListener("DOMContentLoaded", function () {
            updatePagerButtons();
        });

        const itemsPerPage = 7; // Number of items per page
        let currentPage = 1;
        const pagerContainer = document.querySelector('.pager-container');
        const prevButton = pagerContainer.querySelector('.prev');
        const nextButton = pagerContainer.querySelector('.next');
        const pages = pagerContainer.querySelectorAll('.page');

        // Add event listeners to pager buttons
        prevButton.addEventListener('click', handlePaginationClick);
        nextButton.addEventListener('click', handlePaginationClick);
        pages.forEach(page => page.addEventListener('click', handlePaginationClick));

        // Initialize table visibility and pager buttons
        updateTableVisibility();
        updatePagerButtons();
    </script>
</body>

</html>