<?php
header('Content-Type: application/json');
require_once("../db/connection.php");

if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    echo json_encode([]);
    exit;
}

$category_id = intval($_GET['category_id']);

$stmt = $conn->prepare("SELECT id, sub_category FROM item_subcategory WHERE category_id = ? ORDER BY sub_category ASC");
$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();
$subcategories = [];

while ($row = $result->fetch_assoc()) {
    $subcategories[] = $row;
}

$stmt->close();

echo json_encode($subcategories);
