<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO suppliers
        (name, email, phone, address)
        VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $name,
        $email,
        $phone,
        $address
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view.php?added=1");
        exit();
    } else {
        $error = "Failed to add supplier.";
    }
}

include "../includes/header.php";
?>

<h2>Add Supplier</h2>

<?php
if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="POST">

<label>Supplier Name</label>

<input
type="text"
name="name"
required>

<label>Email</label>

<input
type="email"
name="email">

<label>Phone</label>

<input
type="text"
name="phone">

<label>Address</label>

<textarea
name="address"
rows="4"></textarea>

<br><br>

<button type="submit" class="btn">
    Add Supplier
</button>

<a href="view.php" class="btn">
    Cancel
</a>

</form>

<?php
include "../includes/footer.php";
?>