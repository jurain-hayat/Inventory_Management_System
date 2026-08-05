<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Supplier ID not found.");
}

$id = (int)$_GET['id'];

// Get supplier
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM suppliers WHERE supplier_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$supplier = mysqli_fetch_assoc($result);

if (!$supplier) {
    die("Supplier not found.");
}

// Check if supplier is used by any products
$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM products
     WHERE supplier_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($row['total'] > 0) {
        die("This supplier cannot be deleted because products are assigned to it.");
    }

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM suppliers WHERE supplier_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view.php?deleted=1");
        exit();
    } else {
        echo "<p style='color:red;'>Delete failed.</p>";
    }
}

include "../includes/header.php";
?>

<h2>Delete Supplier</h2>

<p>Are you sure you want to delete this supplier?</p>

<table>
    <tr>
        <th>Name</th>
        <td><?php echo htmlspecialchars($supplier['name']); ?></td>
    </tr>

    <tr>
        <th>Email</th>
        <td><?php echo htmlspecialchars($supplier['email']); ?></td>
    </tr>

    <tr>
        <th>Phone</th>
        <td><?php echo htmlspecialchars($supplier['phone']); ?></td>
    </tr>
</table>

<br>

<form method="POST">

    <button
        type="submit"
        onclick="return confirm('Delete this supplier?');">
        Delete Supplier
    </button>

    <a href="view.php">
        <button type="button">Cancel</button>
    </a>

</form>

<?php include "../includes/footer.php"; ?>