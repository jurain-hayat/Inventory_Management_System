<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// =========================
// Dashboard Statistics
// =========================

$productCount = mysqli_fetch_row(
    mysqli_query($conn, "SELECT COUNT(*) FROM products")
)[0];

$supplierCount = mysqli_fetch_row(
    mysqli_query($conn, "SELECT COUNT(*) FROM suppliers")
)[0];

$salesCount = mysqli_fetch_row(
    mysqli_query($conn, "SELECT COUNT(*) FROM sales")
)[0];

$lowStockCount = mysqli_fetch_row(
    mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE quantity <= 5")
)[0];

$totalInventoryValue = mysqli_fetch_row(
    mysqli_query(
        $conn,
        "SELECT IFNULL(SUM(price * quantity),0) FROM products"
    )
)[0];

include "includes/header.php";
?>

<h2>
    Welcome,
    <?php echo htmlspecialchars($_SESSION['username']); ?>
</h2>

<p>
    Role:
    <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>
</p>

<!-- ========================= -->
<!-- Dashboard Cards -->
<!-- ========================= -->

<div class="card-container">

    <a href="products/view.php" class="card">
        <h3>Total Products</h3>
        <h2><?php echo $productCount; ?></h2>
    </a>

    <a href="suppliers/view.php" class="card">
        <h3>Total Suppliers</h3>
        <h2><?php echo $supplierCount; ?></h2>
    </a>

    <a href="sales/view.php" class="card">
        <h3>Total Sales</h3>
        <h2><?php echo $salesCount; ?></h2>
    </a>

    <a href="products/view.php" class="card">
        <h3>Low Stock</h3>
        <h2><?php echo $lowStockCount; ?></h2>
    </a>

    <div class="card">
        <h3>Inventory Value</h3>
        <h2>$<?php echo number_format($totalInventoryValue, 2); ?></h2>
    </div>

</div>

<!-- ========================= -->
<!-- Quick Actions -->
<!-- ========================= -->

<h2 style="margin-top:40px;">Quick Actions</h2>

<p style="margin-top:20px;">

<a class="btn" href="products/view.php">Products</a>

<a class="btn" href="suppliers/view.php">Suppliers</a>

<a class="btn" href="sales/view.php">Sales</a>

<a class="btn" href="auth/logout.php">Logout</a>

</p>

<!-- ========================= -->
<!-- Recent Sales -->
<!-- ========================= -->

<h2 style="margin-top:40px;">Recent Sales</h2>

<div class="table-container">

<table>

<tr>
    <th>Sale ID</th>
    <th>Product</th>
    <th>Quantity</th>
    <th>Total Price</th>
    <th>Date</th>
</tr>

<?php

$result = mysqli_query(
    $conn,
    "SELECT
        s.sale_id,
        p.name,
        s.quantity,
        s.total_price,
        s.sale_date
    FROM sales s
    JOIN products p
        ON s.product_id = p.product_id
    ORDER BY s.sale_date DESC
    LIMIT 5"
);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "<tr>

            <td>{$row['sale_id']}</td>

            <td>" . htmlspecialchars($row['name']) . "</td>

            <td>{$row['quantity']}</td>

            <td>$" . number_format($row['total_price'], 2) . "</td>

            <td>" .
            date("d M Y h:i A", strtotime($row['sale_date']))
            . "</td>

        </tr>";

    }

} else {

    echo "<tr>

        <td colspan='5' style='text-align:center;'>

        No sales found.

        </td>

    </tr>";

}

?>

</table>

</div>

<!-- ========================= -->
<!-- Low Stock -->
<!-- ========================= -->

<h2 style="margin-top:40px;">Low Stock Products</h2>

<div class="table-container">

<table>

<tr>
    <th>Product</th>
    <th>Current Stock</th>
</tr>

<?php

$result = mysqli_query(
    $conn,
    "SELECT
        name,
        quantity
    FROM products
    WHERE quantity <= 5
    ORDER BY quantity ASC"
);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "<tr>

            <td>" .
            htmlspecialchars($row['name'])
            . "</td>

            <td>{$row['quantity']}</td>

        </tr>";

    }

} else {

    echo "<tr>

        <td colspan='2' style='text-align:center;'>

        No low stock products.

        </td>

    </tr>";

}

?>

</table>

</div>

<?php
include "includes/footer.php";
?>