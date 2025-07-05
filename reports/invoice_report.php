<?php
require_once("../db/connection.php");

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = "
    SELECT 
        i.invoice_no,
        i.date,
        c.first_name AS customer_name,
        c.district,
        i.`item_count` AS item_count,
        i.amount AS invoice_amount
    FROM invoice i
    JOIN customer c ON i.customer = c.id
    WHERE 1
";

$params = [];
$types = "";

if ($from && $to) {
    $sql .= " AND i.date BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types .= "ss";
}

$sql .= " ORDER BY i.date DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Prepare Failed: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4 text-center">Invoice Report</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <label for="from" class="col-form-label">From:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="from" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-auto">
            <label for="to" class="col-form-label">To:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="to" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Filter</button>
        </div>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Customer District</th>
                    <th>Item Count</th>
                    <th>Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['district']) ?></td>
                    <td><?= htmlspecialchars($row['item_count']) ?></td>
                    <td><?= number_format($row['invoice_amount'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No invoices found for the selected date range.</div>
    <?php endif; ?>

<a href="../item/list_items.php" class="btn btn-secondary">Back to List</a>
<br>
</body>
</html>

