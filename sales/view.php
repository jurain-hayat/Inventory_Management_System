<?php include '../db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Sales History</h2>

<div class="nav">
    <a href="../dashboard.php">Dashboard</a>
    <a href="add.php"><button>New Sale</button></a>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Product</th>
    <th>Quantity</th>
    <th>Date & Time</th>
    <th>Status</th>
</tr>

<?php
$result = mysqli_query($conn, "
SELECT sales.*, products.name, products.quantity as remaining
FROM sales 
JOIN products ON sales.product_id = products.product_id
");

while ($row = mysqli_fetch_assoc($result)) {

    $status = ($row['remaining'] < 5) ? "Low Stock" : "Available";
    $class = ($row['remaining'] < 5) ? "low-stock" : "";

    echo "<tr>
        <td>{$row['sale_id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['sale_date']}</td>
        <td class='$class'>$status</td>
    </tr>";
}
?>

</table>

</body>
</html>