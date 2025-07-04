<?php
require_once("../db/connection.php");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=customers.xls");

$result = $conn->query("SELECT * FROM customer");

echo "ID\tTitle\tFirst Name\tMiddle Name\tLast Name\tContact Number\tDistrict\n";

while ($row = $result->fetch_assoc()) {
    echo "{$row['id']}\t{$row['title']}\t{$row['first_name']}\t{$row['middle_name']}\t{$row['last_name']}\t{$row['contact_no']}\t{$row['district']}\n";
}
