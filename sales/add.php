<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Get product information
    $stmt = mysqli_prepare(
        $conn,
        "SELECT price, quantity
         FROM products
         WHERE product_id=?"
    );

    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {

        $error = "Product not found.";

    } elseif ($quantity <= 0) {

        $error = "Quantity must be greater than zero.";

    } elseif ($quantity > $product['quantity']) {

        $error = "Not enough stock available.";

    } else {

        $unit_price = $product['price'];
        $total_price = $unit_price * $quantity;

        mysqli_begin_transaction($conn);

        try {

            // Insert sale
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO sales
                (product_id, quantity, unit_price, total_price)
                VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "iidd",
                $product_id,
                $quantity,
                $unit_price,
                $total_price
            );

            mysqli_stmt_execute($stmt);

            // Update stock
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE products
                 SET quantity = quantity - ?
                 WHERE product_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $quantity,
                $product_id
            );

            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);

            header("Location: view.php?added=1");
            exit();

        } catch (Exception $e) {

            mysqli_rollback($conn);
            $error = "Sale failed.";

        }

    }

}

include "../includes/header.php";
?>

<h2>Make Sale</h2>

<?php
if ($error != "") {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="POST">

<label>Product</label>

<select name="product_id" required>

<option value="">Select Product</option>

<?php

$result = mysqli_query(
    $conn,
    "SELECT product_id,name,quantity
    FROM products
    WHERE quantity>0
    ORDER BY name"
);

while($row=mysqli_fetch_assoc($result))
{
    echo "<option value='{$row['product_id']}'>
    {$row['name']} (Stock: {$row['quantity']})
    </option>";
}

?>

</select>

<label>Quantity</label>

<input
type="number"
name="quantity"
min="1"
required>

<br><br>

<button class="btn">
Complete Sale
</button>

<a href="view.php" class="btn">
Cancel
</a>

</form>

<?php include "../includes/footer.php"; ?>