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
$stmt = mysqli_prepare($conn, "SELECT * FROM suppliers WHERE supplier_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$supplier = mysqli_fetch_assoc($result);

if (!$supplier) {
    die("Supplier not found.");
}

// Update supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE suppliers
         SET name=?, email=?, phone=?, address=?
         WHERE supplier_id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $name,
        $email,
        $phone,
        $address,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view.php?updated=1");
        exit();
    } else {
        echo "<p style='color:red;'>Update failed.</p>";
    }
}

include "../includes/header.php";
?>

<h2>Edit Supplier</h2>

<form method="POST">

<label>Name</label>

<input
type="text"
name="name"
value="<?php echo htmlspecialchars($supplier['name']); ?>"
required>

<label>Email</label>

<input
type="email"
name="email"
value="<?php echo htmlspecialchars($supplier['email']); ?>">

<label>Phone</label>

<input
type="text"
name="phone"
value="<?php echo htmlspecialchars($supplier['phone']); ?>">

<label>Address</label>

<textarea
name="address"
rows="4"><?php echo htmlspecialchars($supplier['address']); ?></textarea>

<br><br>

<button type="submit" class="btn">
    Update Supplier
</button>

<a href="view.php" class="btn">
    Cancel
</a>

</form>

<?php include "../includes/footer.php"; ?>