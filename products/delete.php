<?php
include '../db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM products WHERE product_id=$id");
}
header("Location: view.php");
exit;
?>