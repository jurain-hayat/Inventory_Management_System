<?php include '../db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Sale Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Make a Sale</h2>

<div class="nav">
    <a href="view.php">View Sales</a>
</div>

<form method="POST">

Product:
<select name="product_id" required>
<option value="">Select Product</option>
<?php
$res = mysqli_query($conn, "SELECT * FROM products");
while ($row = mysqli_fetch_assoc($res)) {
    echo "<option value='{$row['product_id']}'>
    {$row['name']} (Stock: {$row['quantity']})
    </option>";
}
?>
</select><br><br>

Quantity:
<input type="number" name="quantity" required><br><br>

<button name="sell">Sell</button>

</form>

<?php
if (isset($_POST['sell'])) {

    $pid = $_POST['product_id'];
    $qty = $_POST['quantity'];

    $check = mysqli_query($conn, "SELECT quantity FROM products WHERE product_id=$pid");
    $row = mysqli_fetch_assoc($check);

    if ($row['quantity'] >= $qty) {

        // INSERT WITH CURRENT TIME (FORCE)
        mysqli_query($conn, "INSERT INTO sales (product_id, quantity, sale_date)
        VALUES ($pid, $qty, NOW())");

        // UPDATE STOCK
        mysqli_query($conn, "UPDATE products 
        SET quantity = quantity - $qty 
        WHERE product_id=$pid");

        echo "<p style='color:green;'>Sale Completed!</p>";

    } else {
        echo "<p style='color:red;'>Not enough stock!</p>";
    }
}
?>

</body>
</html>