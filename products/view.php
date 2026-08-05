<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if ($search != "") {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT p.*, s.name AS supplier_name
         FROM products p
         LEFT JOIN suppliers s
         ON p.supplier_id = s.supplier_id
         WHERE p.name LIKE ?
         ORDER BY p.product_id DESC"
    );

    $keyword = "%$search%";

    mysqli_stmt_bind_param($stmt, "s", $keyword);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

} else {

    $result = mysqli_query(
        $conn,
        "SELECT p.*, s.name AS supplier_name
         FROM products p
         LEFT JOIN suppliers s
         ON p.supplier_id = s.supplier_id
         ORDER BY p.product_id DESC"
    );
}

include "../includes/header.php";
?>

<h2>Product List</h2>

<?php
if (isset($_GET['deleted'])) {
    echo "<p style='color:lime;'>✅ Product deleted successfully.</p>";
}

if (isset($_GET['updated'])) {
    echo "<p style='color:lime;'>✅ Product updated successfully.</p>";
}

if (isset($_GET['added'])) {
    echo "<p style='color:lime;'>✅ Product added successfully.</p>";
}
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin:20px 0;">

<form method="GET" style="display:flex;gap:10px;">

<input
type="text"
name="search"
placeholder="Search product..."
value="<?php echo htmlspecialchars($search); ?>">

<button type="submit" class="btn">
Search
</button>

</form>

<a href="add.php" class="btn">
+ Add Product
</a>

</div>

<table>

<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Name</th>
    <th>Description</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Supplier</th>
    <th>Status</th>
    <th>Created</th>
    <th>Action</th>
</tr>

<?php

while ($row = mysqli_fetch_assoc($result)) {

    $status = ($row['quantity'] > 0)
        ? "Available"
        : "Out of Stock";

    echo "<tr>";

    echo "<td>{$row['product_id']}</td>";

    echo "<td>";

    if (!empty($row['image'])) {

        echo "<img
        src='../assets/uploads/" .
        htmlspecialchars($row['image']) .
        "'
        width='70'
        height='70'
        style='object-fit:cover;border-radius:6px;'>";

    } else {

        echo "No Image";
    }

    echo "</td>";

    echo "<td>" . htmlspecialchars($row['name']) . "</td>";

    echo "<td>" . htmlspecialchars($row['description']) . "</td>";

    echo "<td>$" . number_format($row['price'], 2) . "</td>";

    echo "<td>{$row['quantity']}</td>";

    echo "<td>" . htmlspecialchars($row['supplier_name']) . "</td>";

    echo "<td>";

    if ($row['quantity'] > 5) {

        echo "<span style='color:lime;'>Available</span>";

    } elseif ($row['quantity'] > 0) {

        echo "<span style='color:orange;'>Low Stock</span>";

    } else {

        echo "<span style='color:red;'>Out of Stock</span>";
    }

    echo "</td>";

    echo "<td>{$row['created_at']}</td>";

    echo "<td>

        <a class='btn'
        href='edit.php?id={$row['product_id']}'>
        Edit
        </a>

        <a class='btn'
        onclick=\"return confirm('Delete this product?')\"
        href='delete.php?id={$row['product_id']}'>
        Delete
        </a>

    </td>";

    echo "</tr>";
}

?>

</table>

<?php
include "../includes/footer.php";
?>