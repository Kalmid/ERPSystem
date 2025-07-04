<?php
require_once("../db/connection.php");

// Fetch all items
$result = $conn->query("SELECT * FROM item ORDER BY id ASC");

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=items_export_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Output column headers
echo "ID\tItem Code\tItem Name\tCategory\tSubcategory\tQuantity\tUnit Price\n";

// Output data
while ($row = $result->fetch_assoc()) {
    echo $row['id'] . "\t"
        . $row['item_code'] . "\t"
        . $row['item_name'] . "\t"
        . $row['item_category'] . "\t"
        . $row['item_subcategory'] . "\t"
        . $row['quantity'] . "\t"
        . $row['unit_price'] . "\n";
}
exit;
?>
