```php
<?php

session_start();
require_once "../db.php";


/* =========================================================
   LOGIN CHECK
   ========================================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}


/* =========================================================
   CHECK SUPPLIER ID
   ========================================================= */

if (!isset($_GET['id'])) {
    die("Supplier ID not found.");
}

$id = (int) $_GET['id'];


/* =========================================================
   GET SUPPLIER
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM suppliers
     WHERE supplier_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$supplier = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$supplier) {
    die("Supplier not found.");
}


/* =========================================================
   CHECK PRODUCTS
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM products
     WHERE supplier_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

$productCount = (int) $row['total'];


/* =========================================================
   DELETE SUPPLIER
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /*
     * Do not delete a supplier that is being
     * used by products.
     */

    if ($productCount > 0) {

        $error =
            "This supplier cannot be deleted because "
            . $productCount
            . " product(s) are assigned to it.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM suppliers
             WHERE supplier_id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header("Location: view.php?deleted=1");
            exit();

        } else {

            $error = "Failed to delete supplier.";

        }

        mysqli_stmt_close($stmt);
    }
}


/* =========================================================
   PAGE HEADER
   ========================================================= */

include "../includes/header.php";

?>


<!-- PAGE TITLE -->

<div class="page-title">

    <h2>🗑️ Delete Supplier</h2>

</div>


<!-- ERROR -->

<?php if (isset($error)): ?>

    <div class="error">
        ❌ <?php echo htmlspecialchars($error); ?>
    </div>

<?php endif; ?>


<!-- CONFIRMATION -->

<div class="delete-confirmation">

    <p>
        Are you sure you want to delete this supplier?
    </p>


    <?php if ($productCount > 0): ?>

        <div class="error">

            ⚠️ This supplier is currently assigned to

            <strong>
                <?php echo $productCount; ?>
            </strong>

            product(s).

            <br><br>

            You must remove or change those product
            assignments before deleting this supplier.

        </div>

    <?php endif; ?>


    <!-- SUPPLIER INFORMATION -->

    <table class="product-table">

        <tr>

            <th>Name</th>

            <td>
                <?php
                echo htmlspecialchars(
                    $supplier['name']
                );
                ?>
            </td>

        </tr>


        <tr>

            <th>Email</th>

            <td>
                <?php
                echo htmlspecialchars(
                    $supplier['email']
                );
                ?>
            </td>

        </tr>


        <tr>

            <th>Phone</th>

            <td>
                <?php
                echo htmlspecialchars(
                    $supplier['phone']
                );
                ?>
            </td>

        </tr>


        <tr>

            <th>Address</th>

            <td>
                <?php
                echo htmlspecialchars(
                    $supplier['address']
                );
                ?>
            </td>

        </tr>

    </table>


    <br>


    <!-- ACTIONS -->

    <?php if ($productCount === 0): ?>

        <form method="POST">

            <button
                type="submit"
                class="btn delete-btn"
                onclick="return confirm('Are you absolutely sure you want to delete this supplier?');"
            >
                🗑️ Delete Supplier
            </button>

        </form>

    <?php endif; ?>


    <br>

    <a
        href="view.php"
        class="btn"
    >
        ← Cancel
    </a>

</div>


<?php

include "../includes/footer.php";

?>
```
