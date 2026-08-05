<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../includes/header.php";
?>

<h2>Sales History</h2>

<?php
if (isset($_GET['added'])) {
    echo "<p style='color:green;'>✅ Sale completed successfully.</p>";
}
?>

<div class="nav">
    <a href="../dashboard.php">Dashboard</a>
    <a href="add.php"><button>New Sale</button></a>
</div>

<table>

<tr>
    <th>Sale ID</th>
    <th>Product</th>
    <th>Quantity</th>
    <th>Unit Price</th>
    <th>Total Price</th>
    <th>Sale Date</th>
    <th>Remaining Stock</th>
    <th>Status</th>
</tr>

<?php

$query = "
SELECT
    s.sale_id,
    s.quantity,
    s.unit_price,
    s.total_price,
    s.sale_date,
    p.name,
    p.quantity AS remaining_stock
FROM sales s
INNER JOIN products p
ON s.product_id = p.product_id
ORDER BY s.sale_date DESC
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    $status = ($row['remaining_stock'] <= 5)
        ? "Low Stock"
        : "Available";

    $class = ($row['remaining_stock'] <= 5)
        ? "low-stock"
        : "";

    echo "<tr>

        <td>{$row['sale_id']}</td>

        <td>" . htmlspecialchars($row['name']) . "</td>

        <td>{$row['quantity']}</td>

        <td>$" . number_format($row['unit_price'], 2) . "</td>

        <td>$" . number_format($row['total_price'], 2) . "</td>

        <td>{$row['sale_date']}</td>

        <td>{$row['remaining_stock']}</td>

        <td class='$class'>$status</td>

    </tr>";
}

?>

</table>

<?php include "../includes/footer.php"; ?>