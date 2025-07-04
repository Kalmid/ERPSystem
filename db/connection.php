<?php
$host = "localhost";
$user = "root";
$password = ""; // no password usually in XAMPP
$database = "erp_db"; // your actual DB name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
