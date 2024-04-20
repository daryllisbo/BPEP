<?php

include '../database/connect.php';

if(isset($_POST['submit'])){
    $firstName=$_POST['firstName'];
    $lastName=$_POST['lastName'];
    $email=$_POST['email'];
    $password=$_POST['password'];

    $sql="insert into `registration` (firstName, lastName, email, password)
    values ('$firstName', '$lastName', '$email', '$password')";
    $result=mysqli_query($con,$sql);
    if($result) {
        echo "Registered successfully!";
    }else{
        die(mysqli_error( $con ));
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="styless.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    <div class="container">
    <div class="inventory-header">
        <h1><i class="fas fa-user-plus create-icon"></i> Create Account</h1>
    </div>

    <div>
        <h2>Create Account</h2>
        <form method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" placeholder="First Name" name="firstName" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" placeholder="Last Name" name="lastName" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" placeholder="email" name="email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" placeholder="Password" name="password" required>
            </div>
            <button type="submit" name="submit">Save</button>
        </form>
    </div>
</body>
</html>
