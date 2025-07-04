<?php
require_once("../db/connection.php");

$errors = [];
$success = "";

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
</head>
<body class="container mt-5">
    <h2>Add Customer</h2>

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
            <input type="text" name="district" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <script>
    function validateForm() {
        return true;
    }
    </script>
</body>
</html>
