<?php
require_once("../db/connection.php");

if (!isset($_GET['id'])) {
    die("Item ID not provided.");
}

$item_id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM item WHERE id = ?");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    header("Location: list_items.php?deleted=1");
    exit;
} else {
    echo "Error deleting item: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
