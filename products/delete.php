<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Product ID not found.");
}

$id = (int)$_GET['id'];

// Get product information
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE product_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}
// Delete product
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Delete image if it exists
    if (!empty($product['image'])) {

        $imagePath = "../assets/uploads/" . $product['image'];

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete product from database
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM products WHERE product_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {

        header("Location: view.php?deleted=1");
        exit();

    } else {

        echo "<p style='color:red;'>Failed to delete product.</p>";

    }
}
?>

<?php include "../includes/header.php"; ?>

<h2>Delete Product</h2>

<p>
Are you sure you want to delete this product?
</p>

<table border="1" cellpadding="10">
    <tr>
        <th>Name</th>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
    </tr>

    <tr>
        <th>Price</th>
        <td><?php echo $product['price']; ?></td>
    </tr>

    <tr>
        <th>Quantity</th>
        <td><?php echo $product['quantity']; ?></td>
    </tr>
</table>

<br>

<form method="POST">

<button
type="submit"
name="delete"
onclick="return confirm('Are you absolutely sure?');">
    🗑 Delete Product
</button>

    <a href="view.php">
        <button type="button">
            Cancel
        </button>
    </a>

</form>

<?php include "../includes/footer.php"; ?>