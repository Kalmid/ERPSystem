<?php
require_once("../db/connection.php");

$errors = [];
$success = "";

$districts = $conn->query("SELECT id, district FROM district WHERE active='yes' ORDER BY district ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $firstName = $_POST["first_name"];
    $middleName = $_POST["middle_name"];
    $lastName = $_POST["last_name"];
    $contact = $_POST["contact_no"];
    $district = $_POST["district"];

    if (empty($firstName) || empty($lastName) || empty($middleName) || empty($contact) || empty($district)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO customer (title, first_name, middle_name, last_name, contact_no, district) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssssss", $title, $firstName, $middleName, $lastName, $contact, $district);

            if ($stmt->execute()) {
                $success = "Customer registered successfully!";
            } else {
                $errors[] = "Execute failed: " . $stmt->error;
            }
            $stmt->close();
        }
        $conn->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Customer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
</header>

	<br>
    <h2 class="mb-4 text-center">Add Customer</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= $e ?></div>
    <?php endforeach; ?>

    <form method="POST" onsubmit="return validateForm();">
        <div class="mb-3">
            <label>Title</label>
            <select name="title" class="form-select" required>
                <option value="">Select</option>
                <option>Mr</option>
                <option>Mrs</option>
                <option>Miss</option>
                <option>Dr</option>
            </select>
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
		<div class="mb-3">
            <label>Middle Name</label>
            <input type="text" name="middle_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="contact_no" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>District</label>
			<select name="district" class="form-select" required>
				<option value="">Select District</option>
				<?php while ($d = $districts->fetch_assoc()): ?>
					<option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['district']) ?></option>
				<?php endwhile; ?>
			</select>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <script>
	function validateForm() {
    const contactField = document.querySelector('input[name="contact_no"]');
    const contact = contactField.value.trim();

    const phonePattern = /^[0-9]{10}$/;

    if (!phonePattern.test(contact)) {
        alert("Please enter a valid 10-digit contact number (numbers only).");
        contactField.focus();
        return false;
    }

    return true; 
}
</script>

</body>
</html>
