<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Get statistics
$productCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$supplierCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM suppliers"))[0];
$salesCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM sales"))[0];
$lowStockCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE quantity < 5"))[0];

include "includes/header.php";
?>

<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

<p>
Role:
<strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>
</p>

<div class="card-container">

    <div class="card">
        <h3>Total Products</h3>
        <h2><?php echo $productCount; ?></h2>
    </div>

    <div class="card">
        <h3>Total Suppliers</h3>
        <h2><?php echo $supplierCount; ?></h2>
    </div>

    <div class="card">
        <h3>Total Sales</h3>
        <h2><?php echo $salesCount; ?></h2>
    </div>

    <div class="card">
        <h3>Low Stock</h3>
        <h2><?php echo $lowStockCount; ?></h2>
    </div>

</div>

<h2 style="margin-top:40px;">Quick Actions</h2>

<p style="margin-top:20px;">

<a class="btn" href="products/view.php">Products</a>

<a class="btn" href="suppliers/view.php">Suppliers</a>

<a class="btn" href="sales/view.php">Sales</a>

<a class="btn" href="auth/logout.php">Logout</a>

</p>

<?php
include "includes/footer.php";
?>