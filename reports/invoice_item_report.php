<?php
require_once("../db/connection.php");

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = "
SELECT 
    invoice.invoice_no,
    invoice.date,
    CONCAT(customer.first_name, ' ', customer.last_name) AS customer_name,
    item.item_name,
    item.item_code,
    item_category.category AS item_category,
    invoice_master.unit_price
FROM invoice
JOIN customer ON invoice.customer = customer.id
JOIN invoice_master ON invoice.invoice_no = invoice_master.invoice_no
JOIN item ON invoice_master.item_id = item.id
JOIN item_category ON item.item_category = item_category.id
WHERE 1
";

$params = [];
$types = "";

if (!empty($from)) {
    $sql .= " AND invoice.date >= ? ";
    $params[] = $from;
    $types .= "s";
}

if (!empty($to)) {
    $sql .= " AND invoice.date <= ? ";
    $params[] = $to;
    $types .= "s";
}

$sql .= " ORDER BY invoice.date DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice Item Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4 text-center">Invoice Item Report</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>From Date:</label>
            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-3">
            <label>To Date:</label>
            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-3 mt-4">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Item Category</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['item_code']) ?></td>
                    <td><?= htmlspecialchars($row['item_category']) ?></td>
                    <td><?= number_format($row['unit_price'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No records found.</div>
    <?php endif; ?>

<a href="../item/list_items.php" class="btn btn-secondary">Back to List</a>
<br>
</body>
</html>
