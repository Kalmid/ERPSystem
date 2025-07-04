<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../db/connection.php");

if (!isset($_GET['id'])) {
    die("Item ID not provided.");
}

$item_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM item WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    die("Item not found.");
}

$category_result = $conn->query("SELECT id, category FROM item_category ORDER BY category ASC");
$categories = $category_result->fetch_all(MYSQLI_ASSOC);

$selected_category_id = null;
$cat_stmt = $conn->prepare("SELECT id FROM item_category WHERE category = ?");
$cat_stmt->bind_param("s", $item['item_category']);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
if ($cat_result && $cat_result->num_rows > 0) {
    $selected_category_id = $cat_result->fetch_assoc()['id'];
}
$cat_stmt->close();

$selected_subcategory_id = null;
$sub_stmt = $conn->prepare("SELECT id FROM item_subcategory WHERE sub_category = ?");
$sub_stmt->bind_param("s", $item['item_subcategory']);
$sub_stmt->execute();
$sub_result = $sub_stmt->get_result();
if ($sub_result && $sub_result->num_rows > 0) {
    $selected_subcategory_id = $sub_result->fetch_assoc()['id'];
}
$sub_stmt->close();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_code = trim($_POST["item_code"]);
    $item_name = trim($_POST["item_name"]);
    $item_category_id = $_POST["item_category"];
    $item_subcategory_id = $_POST["item_subcategory"];
    $quantity = $_POST["quantity"];
    $unit_price = $_POST["unit_price"];

    if (
        empty($item_code) || empty($item_name) || empty($item_category_id) || empty($item_subcategory_id)
        || $quantity === '' || $unit_price === ''
    ) {
        $errors[] = "All fields are required.";
    } elseif (!is_numeric($quantity) || !is_numeric($unit_price)) {
        $errors[] = "Quantity and Unit Price must be numeric.";
    } else {
        $stmt = $conn->prepare("
            UPDATE item SET item_code=?, item_name=?,
            item_category=(SELECT category FROM item_category WHERE id = ?),
            item_subcategory=(SELECT sub_category FROM item_subcategory WHERE id = ?),
            quantity=?, unit_price=? WHERE id=?
        ");
        $stmt->bind_param("ssiiidi", $item_code, $item_name, $item_category_id, $item_subcategory_id, $quantity, $unit_price, $item_id);

        if ($stmt->execute()) {
            header("Location: edit_item.php?id=$item_id&updated=1");
            exit;
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
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function fetchSubcategories(categoryId, selectedSubId = null) {
            fetch("get_subcategories.php?category_id=" + categoryId)
                .then(res => res.json())
                .then(data => {
                    console.log("Subcategories:", data);
                    const subCatSelect = document.getElementById("item_subcategory");
                    subCatSelect.innerHTML = '<option value="">Select Subcategory</option>';
                    data.forEach(sub => {
                        const option = document.createElement("option");
                        option.value = sub.id;
                        option.text = sub.sub_category;
                        if (sub.id == selectedSubId) {
                            option.selected = true;
                        }
                        subCatSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                });
        }

        window.onload = function () {
            const categorySelect = document.getElementById("item_category");
            const currentCategoryId = "<?= $selected_category_id ?>";
            const currentSubcategoryId = "<?= $selected_subcategory_id ?>";

            categorySelect.addEventListener("change", function () {
                fetchSubcategories(this.value);
            });

            if (currentCategoryId) {
                fetchSubcategories(currentCategoryId, currentSubcategoryId);
            }
        };
    </script>
</head>
<body class="container mt-5">
    <h2>Edit Item</h2>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Item updated successfully.</div>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Item Code</label>
            <input type="text" name="item_code" class="form-control" required value="<?= htmlspecialchars($_POST['item_code'] ?? $item['item_code']) ?>">
        </div>
        <div class="mb-3">
            <label>Item Name</label>
            <input type="text" name="item_name" class="form-control" required value="<?= htmlspecialchars($_POST['item_name'] ?? $item['item_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Item Category</label>
            <select name="item_category" id="item_category" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $selected_category_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Item Sub Category</label>
            <select name="item_subcategory" id="item_subcategory" class="form-select" required>
                <!-- Populated dynamically -->
            </select>
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" required value="<?= htmlspecialchars($_POST['quantity'] ?? $item['quantity']) ?>">
        </div>
        <div class="mb-3">
            <label>Unit Price</label>
            <input type="text" name="unit_price" class="form-control" required value="<?= htmlspecialchars($_POST['unit_price'] ?? $item['unit_price']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Item</button>
        <a href="list_items.php" class="btn btn-secondary">Back to List</a>
    </form>
</body>
</html>
