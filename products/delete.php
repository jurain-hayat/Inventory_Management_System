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
   CHECK PRODUCT ID
   ========================================================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Invalid product ID.");

}

$id = (int) $_GET['id'];


/* =========================================================
   GET PRODUCT
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM products
     WHERE product_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$product) {

    die("Product not found.");

}


/* =========================================================
   DELETE PRODUCT
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* -----------------------------------------------------
       DELETE IMAGE
       ----------------------------------------------------- */

    if (!empty($product['image'])) {

        $imagePath =
            "../assets/uploads/" .
            $product['image'];

        if (file_exists($imagePath)) {

            unlink($imagePath);

        }

    }


    /* -----------------------------------------------------
       DELETE DATABASE RECORD
       ----------------------------------------------------- */

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM products
         WHERE product_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        /*
         * Redirect back to product list.
         *
         * view.php will automatically recalculate
         * the product count and pagination.
         */

        header(
            "Location: view.php?deleted=1"
        );

        exit();

    }


    mysqli_stmt_close($stmt);

    die("Failed to delete product.");

}


/* =========================================================
   HEADER
   ========================================================= */

include "../includes/header.php";

?>


<div class="delete-container">

    <h2>🗑️ Delete Product</h2>

    <p>
        Are you sure you want to delete this product?
    </p>


    <div class="delete-product-info">

        <p>
            <strong>Name:</strong>

            <?php
            echo htmlspecialchars(
                $product['name']
            );
            ?>
        </p>


        <p>
            <strong>Price:</strong>

            ৳<?php
            echo number_format(
                (float) $product['price'],
                2
            );
            ?>
        </p>


        <p>
            <strong>Quantity:</strong>

            <?php
            echo (int) $product['quantity'];
            ?>
        </p>

    </div>


    <form method="POST">

        <button
            type="submit"
            class="btn delete-btn"
            onclick="return confirm('Are you absolutely sure you want to delete this product?');"
        >
            🗑️ Delete Product
        </button>


        <a
            href="view.php"
            class="btn"
        >
            Cancel
        </a>

    </form>

</div>


<?php

include "../includes/footer.php";

?>