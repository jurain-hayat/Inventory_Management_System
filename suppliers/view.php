<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../includes/header.php";
?>

<h2>Supplier List</h2>

<?php
if (isset($_GET['deleted'])) {
    echo "<p style='color:green;'>✅ Supplier deleted successfully.</p>";
}

if (isset($_GET['updated'])) {
    echo "<p style='color:green;'>✅ Supplier updated successfully.</p>";
}

if (isset($_GET['added'])) {
    echo "<p style='color:green;'>✅ Supplier added successfully.</p>";
}
?>

<div class="nav">
    <a href="../dashboard.php">Dashboard</a>
    <a href="add.php"><button>Add Supplier</button></a>
</div>

<table>

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Address</th>
    <th>Action</th>
</tr>

<?php

$result = mysqli_query(
    $conn,
    "SELECT * FROM suppliers ORDER BY supplier_id DESC"
);

while ($row = mysqli_fetch_assoc($result)) {

    echo "<tr>

        <td>{$row['supplier_id']}</td>

        <td>" . htmlspecialchars($row['name']) . "</td>

        <td>" . htmlspecialchars($row['email']) . "</td>

        <td>" . htmlspecialchars($row['phone']) . "</td>

        <td>" . htmlspecialchars($row['address']) . "</td>

        <td>

            <a href='edit.php?id={$row['supplier_id']}'>Edit</a> |

            <a href='delete.php?id={$row['supplier_id']}'>
                Delete
            </a>

        </td>

    </tr>";

}

?>

</table>

<?php
include "../includes/footer.php";
?>