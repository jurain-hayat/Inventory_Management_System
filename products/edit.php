<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if an ID was provided
if (!isset($_GET['id'])) {
    die("Product ID not found.");
}

$id = (int) $_GET['id'];

// Get the product
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE product_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $supplier_id = $_POST['supplier_id'];
    $image = $product['image'];

if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] == 0
) {

    $allowed = ['jpg','jpeg','png','gif'];

    $extension = strtolower(
        pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)
    );

    if (in_array($extension, $allowed)) {

        $image = time() . "_" . basename($_FILES['image']['name']);

        if (!move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/uploads/" . $image
            )) 
        {
            die("Image upload failed.");
        }
    }
}

    $stmt = mysqli_prepare(
    $conn,
    "UPDATE products
    SET name=?, description=?, price=?, quantity=?, supplier_id=?, image=?
    WHERE product_id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssdiisi",
    $name,
    $description,
    $price,
    $quantity,
    $supplier_id,
    $image,
    $id
);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view.php");
        exit();
    } else {
        echo "<p style='color:red;'>Update failed.</p>";
    }
}

// Get suppliers
$suppliers = mysqli_query(
    $conn,
    "SELECT supplier_id, name FROM suppliers ORDER BY name"
);

include "../includes/header.php";
?>

<h2>Edit Product</h2>

<form method="POST" enctype="multipart/form-data">

<label>Product Name</label>

<input
type="text"
name="name"
value="<?php echo htmlspecialchars($product['name']); ?>"
required>

<label>Description</label>

<textarea
name="description"
rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>

<label>Price</label>

<input
type="number"
name="price"
step="0.01"
value="<?php echo $product['price']; ?>"
required>

<label>Quantity</label>

<input
type="number"
name="quantity"
value="<?php echo $product['quantity']; ?>"
required>

<label>Supplier</label>

<select name="supplier_id">

<?php

while($row=mysqli_fetch_assoc($suppliers))
{

$selected="";

if($row['supplier_id']==$product['supplier_id'])
{
    $selected="selected";
}

echo "<option
value='{$row['supplier_id']}'
$selected>

{$row['name']}

</option>";

}

?>

</select>

<br><br>

<label>Current Image</label><br>

<?php if (!empty($product['image'])) { ?>

    <img
        src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>"
        alt="Product Image"
        width="150">

<?php } else { ?>

    <p>No image available.</p>

<?php } ?>

<br><br>

<label>Choose New Image</label>

<input
type="file"
name="image"
accept=".jpg,.jpeg,.png,.gif">

<br><br>

<button type="submit" class="btn">
    Update Product
</button>

<a href="view.php" class="btn">
    Cancel
</a>

</form>

<?php
include "../includes/footer.php";
?>