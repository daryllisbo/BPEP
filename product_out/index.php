<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Out</title>
    <link rel="stylesheet" href="product_out_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>

    <div class="product-sidebar">
        <div class="product-logo-container">
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
    
    
    <div class="container product-out-container">
        <div class="conth1">
            <h1><i class="fas fa-truck"></i> Product Out</h1>
        </div>
        <div class="scrollable-content">
        <form method="POST" action="deduct_inventory.php" id="productOutForm">
        <div class="product-form-group">
    <label for="po-number">PO # <span class="required">*</span></label>
    <input type="text" id="po-number" name="po-number" placeholder="Enter PO number" required>
</div>
            <div class="product-form-group">
            <label for="client-name">Client Name <span class="required">*</span></label>
            <input type="text" id="client-name" name="supplier-name" placeholder="Enter supplier name" required>
            </div>
            <div class="product-form-group">
            <label for="product-code">Product Code <span class="required">*</span></label>
            <input type="text" id="product-code" placeholder="Enter product code" name="product-code[]" required>
            </div>
            <div class="product-form-group">
            <label for="brand">Brand <span class="required">*</span></label>
            <input type="text" id="brand" name="brand" placeholder="Autofilled based on product code" readonly>
            </div>
            <div class="product-form-group">
            <label for="model-description">Model/Description <span class="required">*</span></label>
            <input type="text" name="model-description" id="model-description" placeholder="Autofilled based on product code" readonly>
            </div>
            <div class="product-form-group">
            <label for="supply-date">Supply Date <span class="required">*</span></label>
            <input type="date" id="supply-date" name="supply-date" required>
            </div>
            <div class="product-form-group">
            <label for="remarks">Remarks</label>
            <input type="text" id="remarks" placeholder="(optional)">
            </div>
            <div class="product-form-group">
            <label>Serial Number <span class="required">*</span></label>
        <div id="serial-number" class="serial-number" contenteditable="true">
        <!-- Example serial numbers; remove these lines -->
        <!-- <div>PGH3236002200</div> -->
        <!-- <div>PGH323600227</div> -->
        <!-- New serial numbers will be added here -->
    </div>
    <div class="product-form-group quantity">
    <label for="quantity">Quantity <span class="required">*</span></label>
    <input type="number" id="quantity" placeholder="0" name="quantity[]" readonly>
</div>
</div>
</div>
    <!-- Container for additional items -->
    <div id="additional-items-container-product"></div>
        <div class="btn-group-product">
            <div class="two-button-product">
                <button type="button" class="btn add-item">Add Item</button>
                <button type="button" class="btn export">Export</button>
                <button type="submit" class="btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const addBtn = document.querySelector('.product-out-container .btn.add-item');
    const additionalItemsContainer = document.getElementById('additional-items-container-product');

    // Function to add event listener for auto-filling model description
    function addAutoFillListener(productCodeInput, modelDescriptionInput, brandInput) {
        productCodeInput.addEventListener('input', function() {
            const productCode = this.value;

            if (productCode.length === 0) {
                modelDescriptionInput.value = ""; // Clear model description if product code is empty
                brandInput.value = "";
                return;
            }

            // Make an AJAX request to get the model description based on the product code
            fetch(`get_product_details.php?product_code=${productCode}`)
                .then(response => response.json())
                .then(data => {
                    modelDescriptionInput.value = data.model_description; // Auto-fill the model description
                    brandInput.value = data.brand;
                            brandInput.style.fontWeight = "bold";
                    modelDescriptionInput.style.fontWeight = "bold";
                })
                .catch(error => console.error('Error:', error));
        });
    }

    // Function to calculate and display the quantity for the original set
    function updateQuantity() {
    const serialNumbersContainer = document.getElementById('serial-number');
    const quantityInput = document.getElementById('quantity');

    // Get all lines from the serial numbers container
    const lines = serialNumbersContainer.innerText.split('\n');

    // Count only non-empty lines
    const quantity = lines.filter(line => line.trim().length > 0).length;

    // Update the quantity input field
    quantityInput.value = quantity;
}


    // Function to calculate and display the quantity for additional items
    function updateAdditionalItemQuantity(item) {
    const serialNumbersContainer = item.querySelector('.new-serial-numbers');
    const quantityInput = item.querySelector('.new-quantity');

    // Get all lines from the serial numbers container
    const lines = serialNumbersContainer.innerText.split('\n');

    // Count only non-empty lines
    const quantity = lines.filter(line => line.trim().length > 0).length;

    // Update the quantity input field
    quantityInput.value = quantity;
    }
    // Add event listener for input in the serial number container of the original set
    document.getElementById('serial-number').addEventListener('input', updateQuantity);

    // Get the original product code and model description inputs
    const productCodeInput = document.getElementById('product-code');
    const modelDescriptionInput = document.getElementById('model-description');
    const productOutAddBtn = document.querySelector('.product-out-container .btn.add-item');
    const productOutAdditionalItemsContainer = document.getElementById('additional-items-container-product');
    const brandInput = document.getElementById('brand'); // Get the brand input field

    // Add auto-fill listener for the original set
    addAutoFillListener(productCodeInput, modelDescriptionInput, brandInput);

    addBtn.addEventListener('click', function() {
        // Create new input fields for the additional item
        const newInputFields = `
        <div class="additional-item-product">
            <button type="button" class="remove-item">X</button>
            <div class="product-form-group">
                <label for="product-code">Product Code</label>
                <input type="text" class="new-product-code" placeholder="Enter product code" name="product-code[]">
            </div>
            <div class="product-form-group">
            <label for="brand">Brand <span class="required">*</span></label>
            <input type="text" class="new-brand" placeholder="Autofilled based on product code" readonly>
        </div>
            <div class="product-form-group">
                <label for="model-description">Model/Description</label>
                <input type="text" class="new-model-description" placeholder="Autofilled based on product code" readonly>
            </div>
            <div class="product-form-group">
                <label>Serial Numbers</label>
                <div class="new-serial-numbers" contenteditable="true">
                    <!-- New serial numbers will be added here -->
                </div>
            </div>
            <div class="product-form-group quantity">
                <label for="quantity">Quantity</label>
                <input type="number" class="new-quantity" placeholder="0" name="quantity[]" readonly>
            </div>
        </div>
        `;

        // Append new input fields to the additional items container
        productOutAdditionalItemsContainer.insertAdjacentHTML('beforeend', newInputFields);
        
        // You should define newItemContainer here as the last child of the additionalItemsContainer.
        const newItemContainer = additionalItemsContainer.lastElementChild;

        // Get the newly added product code and model description inputs
        const newProductCodeInput = newItemContainer.querySelector('.new-product-code');
        const newModelDescriptionInput = newItemContainer.querySelector('.new-model-description');
        const newBrandInput = newItemContainer.querySelector('.new-brand'); // This line was previously missing the 'newItemContainer' part.

        // Add auto-fill listener for the newly added item
        addAutoFillListener(newProductCodeInput, newModelDescriptionInput, newBrandInput);

        // Add event listener for input in the serial number container of the added item
        newItemContainer.querySelector('.new-serial-numbers').addEventListener('input', function () {
            updateAdditionalItemQuantity(newItemContainer);
        });
        
        // Attach the remove functionality to the remove button
        newItemContainer.querySelector('.remove-item').addEventListener('click', function() {
            this.parentElement.remove();
        });

        // Update quantity for the newly added item
        updateAdditionalItemQuantity(newItemContainer);
    });

    const exportBtn = document.querySelector('.btn.export');
    exportBtn.addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'p',
        unit: 'mm',
        format: 'a4'
    });

    // Set up the font and styles
    doc.setFont('helvetica');
    doc.setFontSize(10);

    // Title
    doc.text('STOCK REQUEST', 105, 15, { align: 'center' });

    // Date
    const supplyDate = document.getElementById('supply-date').value;
    doc.text(`${supplyDate}`, 170, 30); // Adjust the x-coordinate as needed

    // Get the PO number value
const poNumber = document.getElementById('po-number').value;

// Add PO number to the PDF content
doc.text(`PO #: ${poNumber}`, 20, 15); // Adjust the coordinates as needed

    // Supplier Name
    const supplierName = document.getElementById('client-name').value;
    doc.text(`Supplier Name: ${supplierName}`, 20, 30);

    // Date (placeholder for now)
    doc.text('Date: ', 160, 30);

    // Product Code and Model/Description
    const productCode = document.getElementById('product-code').value;
    const modelDescription = document.getElementById('model-description').value;
    const quantity = document.getElementById('quantity').value; // Retrieve the quantity value
    doc.text(`Product Code: ${productCode}`, 20, 45);
    doc.text(`Model/Description: ${modelDescription}`, 20, 50);
    doc.text(`Quantity: ${quantity}`, 20, 55); // Add the quantity to the PDF content

    // Serial Number Title
    doc.text('SERIAL NUMBER', 20, 60);

    // Serial numbers from original fields
    const serialNumbersText = document.getElementById('serial-number').innerText.trim();
    const serialNumbers = serialNumbersText.split('\n');

    // Define column width and row height
    const columnWidth = 55;
    const rowHeight = 7;

    // Initialize variables for positioning
    let currentColumn = 0;
    let currentRow = 0;
    let startX = 20;
    let startY = 70;

    // Define max serials per page
    let maxSerialsPerPage = 75;
    let serialNumbersPerPage = 0;

    // Loop through serial numbers from original fields and distribute them into columns on the first page
    for (let i = 0; i < serialNumbers.length; i++) {
        // Add a new page if we reach the limit and reset counters
        if (serialNumbersPerPage >= maxSerialsPerPage) {
            doc.addPage();
            currentColumn = 0;
            currentRow = 0;
            startY = 20;
            if (maxSerialsPerPage === 48) {
                maxSerialsPerPage = 60;
            }
            serialNumbersPerPage = 0;

            // Add line on the new page instead of box around original fields
            doc.setLineWidth(0.5);
            doc.line(startX - 5, startY - 30, startX + 175, startY - 30);
        }

        const xPos = startX + currentColumn * columnWidth;
        const yPos = startY + currentRow * rowHeight;

        doc.rect(xPos, yPos - 3, columnWidth, rowHeight);
        doc.text(serialNumbers[i], xPos + 2, yPos + 2);

        currentColumn++;
        if (currentColumn >= 3) {
            currentColumn = 0;
            currentRow++;
        }

        serialNumbersPerPage++;
    }

    // Additional items
    const additionalItems = document.querySelectorAll('.additional-item-product');

    // Ensure the startY is positioned after the last line of the original items
    startY = startY + Math.ceil(serialNumbers.length / 3) * rowHeight + 10;

    // Loop through additional items to add titles and serial number boxes to the PDF
    additionalItems.forEach((item, itemIndex) => {
        // Extract values for additional items
        const additionalProductCode = item.querySelector('.new-product-code').value;
        const additionalModelDescription = item.querySelector('.new-model-description').value;
        const additionalSerialNumbersText = item.querySelector('.new-serial-numbers').innerText.trim();
        const additionalSerialNumbers = additionalSerialNumbersText.split('\n');
        const additionalQuantity = item.querySelector('.new-quantity').value; // Retrieve the quantity for the additional item
        
        // Positioning startY for the line above additional items, a little closer to the original items
        startY -= 5; // Reduce the gap above the first additional item's title

        // Draw line above the first additional item, which is closer to the last of the original fields
        doc.setLineWidth(0.5);
        doc.line(startX - 5, startY, startX + 175, startY);

        // Calculate Y position for the titles of additional items
        let titleY = startY + 10; // Set the title a bit below the line

        // Add titles for additional items

        doc.text(`Product Code: ${additionalProductCode}`, 20, titleY);
        doc.text(`Model/Description: ${additionalModelDescription}`, 20, titleY + 5);
        doc.text(`Quantity: ${additionalQuantity}`, 20, titleY + 10); // Add the quantity for the additional item to the PDF content
        doc.text('SERIAL NUMBER', 20, titleY + 15);

        // Adjust startY for the first serial number box of additional items
        startY = titleY + 20;

        // Reset column and row for each additional item
        currentColumn = 0;
        currentRow = 0;

        // Loop through additional item's serial numbers and add them to the PDF
        additionalSerialNumbers.forEach((serial, index) => {
            // Check if adding this serial number box will overflow to the next page
            if (startY + currentRow * rowHeight >= doc.internal.pageSize.height - 10) {
                doc.addPage();
                startY = 20; // Reset startY for the new page
                currentColumn = 0;
                currentRow = 0;

                // Draw line on the new page instead of box around original fields
                doc.setLineWidth(0.5);
                doc.line(startX - 5, startY - 30, startX + 175, startY - 30);



                // Adjust startY for the first serial number box of additional items on the new page
                startY += 10;
            }

            // Update the position for each serial number box
            const xPos = startX + currentColumn * columnWidth;
            const yPos = startY + currentRow * rowHeight;

            // Draw the box and add the serial number text
            doc.rect(xPos, yPos, columnWidth, rowHeight);
            doc.text(serial, xPos + 2, yPos + 5);

            // Update column and row counters
            currentColumn++;
            if (currentColumn >= 3) {
                currentColumn = 0;
                currentRow++;
            }
        });

        // Update startY for the next additional items or content
        startY += currentRow * rowHeight + rowHeight;

        startY += 10;
       
        // Draw line after the last added item, to close off the section
        doc.setLineWidth(0.5);
        doc.line(startX - 5, startY, startX + 170, startY);

        // Ensure there is a small gap after the final line before any further content or additional items
        startY += 5;
    });

    // Remarks
    const remarks = document.getElementById('remarks').value;

    // Check if there's enough space for the remarks section
    const remainingSpace = doc.internal.pageSize.height - (startY + 60); // Height of bottom content (Remarks, Prepared by, Approved by)

    if (remainingSpace < 0) {
        // Add a new page for the remarks and remaining content
        doc.addPage();

        // Reset startY for remarks and remaining content on the new page
        startY = 20;

    }

    // Add remarks, Prepared By, and Approved By sections
    doc.text('Remarks:', 20, startY);
    doc.text(remarks, 20, startY + 5);

    doc.text('Prepared by: ______________', 20, startY + 15);
    doc.text('Approved by: ______________', 140, startY + 15);

    // Save the PDF file
    doc.save('stock-request.pdf');
    logExportAction(`Exported product out data to PDF for PO#: ${poNumber}, Supplier: ${supplierName}`);

    function logExportAction(description) {
    fetch('log_export_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=exported_product_out_pdf&description=${encodeURIComponent(description)}`
    })
    .then(response => response.text())
    .then(data => console.log("Log response: ", data))
    .catch(error => console.error('Error logging export action:', error));
}
});


});

// Add event listener to the form submission
const productOutForm = document.getElementById('productOutForm');
    productOutForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(productOutForm); // Collect form data
        const additionalItemContainers = document.querySelectorAll('.additional-item-product');

        // Loop through additional items to collect their data
        additionalItemContainers.forEach(container => {
            const inputs = container.querySelectorAll('input, textarea, select');

            inputs.forEach(input => {
                if (input.name) {
                    formData.append(input.name, input.value); // Append data to FormData object
                }
            });
        });

        // Send the form data via fetch to the server
        fetch(productOutForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Display response from server
            window.location.href = '../homepage/index.php'; // Redirect if needed
        })
        .catch(error => {
            alert('An error occurred: ' + error.message);
        });
    });



</script>

</body>
</html>
