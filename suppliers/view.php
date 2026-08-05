<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Suppliers</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Suppliers</h2>

<div class="nav">
    <a href="../dashboard.php">Dashboard</a>
    <a href="add.php"><button>Add Supplier</button></a>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Contact</th>
    <th>Action</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM suppliers");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$row['supplier_id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['contact']}</td>
        <td>
            <a href='delete.php?id={$row['supplier_id']}'>Delete</a>
        </td>
    </tr>";
}
?>

</table>
</body>
</html>