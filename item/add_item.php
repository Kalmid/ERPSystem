<?php
require_once("../db/connection.php");

$errors = [];
$success = "";

$category_result = $conn->query("SELECT id, category FROM item_category ORDER BY category ASC");
$categories = $category_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_code = trim($_POST["item_code"]);
    $item_name = trim($_POST["item_name"]);
    $item_category_id = $_POST["item_category"] ?? '';
    $item_subcategory_id = $_POST["item_subcategory"] ?? '';
    $quantity = $_POST["quantity"];
    $unit_price = $_POST["unit_price"];

    if (empty($item_code) || empty($item_name) || empty($item_category_id) || empty($item_subcategory_id) || $quantity === '' || $unit_price === '') {
        $errors[] = "All fields are required.";
    } elseif (!is_numeric($quantity) || !is_numeric($unit_price)) {
        $errors[] = "Quantity and Unit Price must be numeric.";
    } else {
        $stmt = $conn->prepare("INSERT INTO item (item_code, item_name, item_category, item_subcategory, quantity, unit_price) 
            VALUES (?, ?, (SELECT category FROM item_category WHERE id = ?), (SELECT sub_category FROM item_subcategory WHERE id = ?), ?, ?)");
        $stmt->bind_param("ssiidd", $item_code, $item_name, $item_category_id, $item_subcategory_id, $quantity, $unit_price);

        if ($stmt->execute()) {
            $success = "Item added successfully.";
            $_POST = [];
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
function fetchSubcategories(categoryId) {
    fetch("get_subcategories.php?category_id=" + categoryId)
        .then(res => res.json())
        .then(data => {
            console.log("Loaded subcategories:", data);
            const subCatSelect = document.getElementById("item_subcategory");
            subCatSelect.innerHTML = '<option value="">Select Subcategory</option>';
            data.forEach(sub => {
                const option = document.createElement("option");
                option.value = sub.id;
                option.text = sub.sub_category;
                subCatSelect.appendChild(option);
            });
        })
        .catch(err => {
            console.error("Subcategory load error:", err);
        });
}

window.onload = function () {
    const catSelect = document.getElementById("item_category");
    catSelect.addEventListener("change", function () {
        fetchSubcategories(this.value);
    });

    const selectedCategory = "<?= $_POST['item_category'] ?? '' ?>";
    const selectedSub = "<?= $_POST['item_subcategory'] ?? '' ?>";
    if (selectedCategory) {
        fetch("get_subcategories.php?category_id=" + selectedCategory)
            .then(res => res.json())
            .then(data => {
                const subCatSelect = document.getElementById("item_subcategory");
                subCatSelect.innerHTML = '<option value="">Select Subcategory</option>';
                data.forEach(sub => {
                    const option = document.createElement("option");
                    option.value = sub.id;
                    option.text = sub.sub_category;
                    if (sub.id == selectedSub) option.selected = true;
                    subCatSelect.appendChild(option);
                });
            });
    }
};
</script>

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
    <h2 class="mb-4 text-center">Add New Item</h2>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Item Code</label>
            <input type="text" name="item_code" class="form-control" required value="<?= htmlspecialchars($_POST['item_code'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>Item Category</label>
            <select name="item_category" id="item_category" class="form-select" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (($_POST['item_category'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Item Sub Category</label>
            <select name="item_subcategory" id="item_subcategory" class="form-select" required>
                <option value="">Select subcategory</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Item Name</label>
            <input type="text" name="item_name" class="form-control" required value="<?= htmlspecialchars($_POST['item_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" required value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>Unit Price</label>
            <input type="text" name="unit_price" class="form-control" required value="<?= htmlspecialchars($_POST['unit_price'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Add Item</button>
        <a href="list_items.php" class="btn btn-secondary">Back to List</a>
    </form>
</body>
</html>
