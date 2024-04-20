<?php
include './database/connect.php';

$error_message = ''; // Initialize error message variable

if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to check if the user exists
    // Use prepared statements to avoid SQL injection
    $stmt = $con->prepare("SELECT * FROM `registration` WHERE email=? AND password=?"); // Use password_hash and password_verify in production
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, authentication successful
        $user = $result->fetch_assoc(); // Fetch user data
        session_start(); // Start the session
        $_SESSION['user_id'] = $user['id']; // Store user ID in session variable
        $_SESSION['firstName'] = $user['firstName']; // Store first name in session variable

        // Insert login action into activity_logs
        $action = "Logged in";
        $log_sql = $con->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
        $log_sql->bind_param("is", $user['id'], $action);
        $log_sql->execute();

        if ($log_sql->affected_rows > 0) {
            header("Location: ./homepage/index.php"); // Redirect to the homepage if login is successful
            exit(); // Important to stop further execution
        } else {
            // Log error handling
            $error_message = '<div class="error-message">Failed to log activity.</div>';
        }
    } else {
        // User does not exist or incorrect password
        $error_message = '<div class="error-message">Invalid username or password</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php echo $error_message; ?> <!-- Display error message if present -->
        <h1>Inventory Login</h1>
        <img src="./img/bpep_logo.png" alt="Company Logo" class="logo">
        <form method="post" id="login-form">
            <div class="input-icon">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-button" name="submit"> <i class="fa fa-check"></i> Login</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const errorMessage = document.querySelector('.error-message');

        form.addEventListener('submit', function(event) {
            errorMessage.style.display = 'none'; // Hide the error message before checking

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Check if username and password are empty (just a simple check, you may need more validation)
            if (!username || !password) {
                errorMessage.style.display = 'block'; // Show the error message if fields are empty
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>
</body>
</html>
