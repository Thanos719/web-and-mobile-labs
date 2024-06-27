<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ds_estate";

// Έλεγχος σύνδεσης
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
