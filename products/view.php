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
        WHERE p.name LIKE ?"
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
    echo "<p style='color:green;'>✅ Product deleted successfully.</p>";
}
?>

<form method="GET" style="margin:20px 0;">

<input
type="text"
name="search"
placeholder="Search product..."
value="<?php echo htmlspecialchars($search); ?>">

<button type="submit">Search</button>

<a class="btn" href="add.php">+ Add Product</a>

</form>

<table>

<tr>

<th>ID</th>

<th>Name</th>

<th>Description</th>

<th>Price</th>

<th>Quantity</th>

<th>Supplier</th>

<th>Status</th>

<th>Action</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result)){

$status =
($row['quantity']>0)
?
"Available"
:
"Out of Stock";

echo "

<tr>

<td>{$row['product_id']}</td>

<td>{$row['name']}</td>

<td>{$row['description']}</td>

<td>$ {$row['price']}</td>

<td>{$row['quantity']}</td>

<td>{$row['supplier_name']}</td>

<td>$status</td>

<td>

<a class='btn'
href='edit.php?id={$row['product_id']}'>
Edit
</a>

<a
class='btn'
onclick=\"return confirm('Delete this product?')\"
href='delete.php?id={$row['product_id']}'>
Delete
</a>

</td>

</tr>

";

}

?>

</table>

<?php
include "../includes/footer.php";
?>