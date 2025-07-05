<?php
require_once("../db/connection.php");

$search = $_GET['search'] ?? '';
$searchTerm = "%$search%";

$stmt = $conn->prepare("
    SELECT * FROM item 
    WHERE item_code LIKE ? OR item_name LIKE ? 
    OR item_category LIKE ? OR item_subcategory LIKE ?
    ORDER BY id DESC
");
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<header style="display: flex; justify-content: space-between; padding: 10px 20px; background-color: #f9fafa; border-bottom: 1px solid #ddd;">
  <nav>
    <a href="../customer/list_customers.php" style="margin-right: 15px; text-decoration: none; color: #555;">Customers</a>
    <a href="../item/list_items.php" style="margin-right: 15px; text-decoration: none; color: #555;">Items</a>
<a href="../reports/item_report.php" style="margin-right: 15px; text-decoration: none; color: #555;">Item Report</a>
<a href="../reports/invoice_item_report.php" style="margin-right: 15px; text-decoration: none; color: #555;">Invoice Item Report</a>
<a href="../reports/invoice_report.php" style="margin-right: 15px; text-decoration: none; color: #555;">Invoice Report</a>
  </nav>
</header>

	<br>
    <h2 class="mb-4 text-center">All Items</h2>
    <a href="add_item.php" class="btn btn-success mb-3">+ Add New Item</a>

    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search by code, name, category..." value="<?= htmlspecialchars($search) ?>" />
    </form>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Item deleted successfully.</div>
    <?php endif; ?>

    <a href="export_items_excel.php" class="btn btn-outline-success mb-3">Export to Excel</a>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['item_code']) ?></td>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['item_category']) ?></td>
                    <td><?= htmlspecialchars($row['item_subcategory']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($row['unit_price'], 2) ?></td>
                    <td>
                        <a href="edit_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No items found.</div>
    <?php endif; ?>
</body>
</html>
