<?php
require_once("../db/connection.php");

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM customer WHERE first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR contact_no LIKE ? OR district LIKE ? ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$like = "%$search%";
$stmt->bind_param("sssss", $like, $like, $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

// $sql = "SELECT * FROM customer ORDER BY id DESC";
// $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Customers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 
</head>
<body class="container mt-5">

<header style="display: flex; justify-content: space-between; padding: 10px 20px; background-color: #f9fafa; border-bottom: 1px solid #ddd;">
  <nav>
    <a href="../customer/list_customers.php" style="margin-right: 15px; text-decoration: none; color: #555;">Customers</a>
    <a href="../item/list_items.php" style="text-decoration: none; color: #555;">Items</a>
<a href="../reports/item_report.php" style="margin-right: 15px; text-decoration: none; color: #555;">Item Report</a>
<a href="../reports/invoice_item_report.php" style="margin-right: 15px; text-decoration: none; color: #555;">Invoice Item Report</a>
<a href="../reports/invoice_report.php" style="margin-right: 15px; text-decoration: none; color: #555;">Invoice Report</a>
  </nav>
</header>

	<br>
    <h2 class="mb-4 text-center">Customer List</h2>
    <a href="add_customer.php" class="btn btn-success mb-3">+ Add New Customer</a>

<form method="GET" class="mb-3">
    <input type="text" name="search" placeholder="Search by name/contact/district" value="<?= htmlspecialchars($search) ?>" class="form-control" />
</form>


    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Contact Number</th>
                    <th>District</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['first_name'] ?></td>
                    <td><?= $row['middle_name'] ?></td>
                    <td><?= $row['last_name'] ?></td>
                    <td><?= $row['contact_no'] ?></td>
                    <td><?= $row['district'] ?></td>
                    
                    <td>
                        <a href="edit_customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No customers found.</div>
    <?php endif; ?>

<a href="export_customers_excel.php" class="btn btn-outline-success mb-3">Export to Excel</a>

</body>
</html>
