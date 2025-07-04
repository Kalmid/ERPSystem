<?php
require_once("../db/connection.php");

if (!isset($_GET['id'])) {
    die("Customer ID is required.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM customer WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list_customers.php?deleted=1");
} else {
    echo "Delete failed: " . $stmt->error;
}
$stmt->close();
$conn->close();
