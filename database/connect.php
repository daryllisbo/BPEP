<?php

$con=new mysqli('localhost', 'root', '', 'inventory');

if(!$con) {
    die(mysqli_error($con));
}
?>

