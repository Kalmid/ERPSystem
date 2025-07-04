<?php
require_once("../db/connection.php");

if (!isset($_GET['id'])) {
    die("Customer ID not provided.");
}

$id = $_GET['id'];
$errors = [];
$success = "";

$stmt = $conn->prepare("SELECT * FROM customer WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if (!$customer) {
    die("Customer not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $first = $_POST["first_name"];
    $middle = $_POST["middle_name"];
    $last = $_POST["last_name"];
    $contact = $_POST["contact_no"];
    $district = $_POST["district"];

    if (empty($first) || empty($last) || empty($contact) || empty($district)) {
        $errors[] = "Required fields cannot be empty.";
    } else {
        $stmt = $conn->prepare("UPDATE customer SET title=?, first_name=?, middle_name=?, last_name=?, contact_no=?, district=? WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $first, $middle, $last, $contact, $district, $id);

        if ($stmt->execute()) {
            $success = "Customer updated successfully.";
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Edit Customer</h2>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= $e ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Title</label>
            <select name="title" class="form-select" required>
                <option <?= $customer['title'] == 'Mr' ? 'selected' : '' ?>>Mr</option>
                <option <?= $customer['title'] == 'Mrs' ? 'selected' : '' ?>>Mrs</option>
                <option <?= $customer['title'] == 'Miss' ? 'selected' : '' ?>>Miss</option>
                <option <?= $customer['title'] == 'Dr' ? 'selected' : '' ?>>Dr</option>
            </select>
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?= $customer['first_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="<?= $customer['middle_name'] ?>">
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?= $customer['last_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="contact_no" class="form-control" value="<?= $customer['contact_no'] ?>" required>
        </div>
        <div class="mb-3">
            <label>District</label>
            <input type="text" name="district" class="form-control" value="<?= $customer['district'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="list_customers.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>
