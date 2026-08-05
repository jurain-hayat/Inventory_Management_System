<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Supplier</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Add Supplier</h2>
<div class="nav">
    <a href="view.php">Back to Suppliers</a>
</div>

<form method="POST" style="text-align:center;">
    Name: <input type="text" name="name" required><br><br>
    Contact: <input type="text" name="contact" required><br><br>
    <button name="add">Add Supplier</button>
</form>

<?php
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    mysqli_query($conn, "INSERT INTO suppliers (name, contact) VALUES ('$name','$contact')");
    echo "<p style='color:green;text-align:center;'>Supplier Added!</p>";
}
?>
</body>
</html>