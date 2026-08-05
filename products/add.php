<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

// Load suppliers
$suppliers = mysqli_query($conn, "SELECT supplier_id, name FROM suppliers ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $supplier = $_POST['supplier'];

    $imageName = "";

    if (!empty($_FILES["image"]["name"])) {

        $imageName = time() . "_" . basename($_FILES["image"]["name"]);

        move_uploaded_file(
            $_FILES["image"]["tmp_name"],
            "../assets/uploads/" . $imageName
        );
    }

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products
        (name, description, price, quantity, supplier_id, image)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssdiis",
        $name,
        $description,
        $price,
        $quantity,
        $supplier,
        $imageName
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: view.php");
        exit();

    } else {

        $message = "Error adding product.";

    }
}

include "../includes/header.php";
?>

<h2>Add Product</h2>

<?php
if ($message != "") {
    echo "<p style='color:red;'>$message</p>";
}
?>

<form method="POST" enctype="multipart/form-data">

<label>Product Name</label>
<input type="text" name="name" required>

<label>Description</label>
<textarea name="description"></textarea>

<label>Price</label>
<input type="number" step="0.01" name="price" required>

<label>Quantity</label>
<input type="number" name="quantity" required>

<label>Supplier</label>
<select name="supplier" required>

<?php
while ($row = mysqli_fetch_assoc($suppliers)) {
    echo "<option value='{$row['supplier_id']}'>{$row['name']}</option>";
}
?>

</select>

<label>Product Image</label>
<input type="file" name="image" accept="image/*">

<br><br>

<button type="submit">Save Product</button>

</form>

<?php
include "../includes/footer.php";
?>