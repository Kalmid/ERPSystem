<?php
require_once("../db/connection.php");

$sql = "
SELECT 
    i.item_name,
    c.category AS item_category,
    s.sub_category AS item_subcategory,
    i.quantity
FROM item i
JOIN item_category c ON i.item_category = c.id
JOIN item_subcategory s ON i.item_subcategory = s.id
GROUP BY i.item_name
ORDER BY i.item_name ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4 text-center">Item Report</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['item_category']) ?></td>
                    <td><?= htmlspecialchars($row['item_subcategory']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No items found.</div>
    <?php endif; ?>
</body>
</html>
