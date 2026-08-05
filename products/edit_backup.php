<?php
include '../db.php';

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM products WHERE product_id=$id");
$row = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    mysqli_query($conn, "UPDATE products SET name='$name', price='$price', quantity='$quantity' WHERE product_id=$id");

    header("Location: view.php");
}
?>

<form method="POST">
    Name: <input type="text" name="name" value="<?php echo $row['name']; ?>"><br>
    Price: <input type="text" name="price" value="<?php echo $row['price']; ?>"><br>
    Quantity: <input type="text" name="quantity" value="<?php echo $row['quantity']; ?>"><br>
    <button name="update">Update</button>
</form>