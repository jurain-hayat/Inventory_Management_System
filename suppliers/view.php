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
   GET SUPPLIERS
   ========================================================= */

$query = "
    SELECT *
    FROM suppliers
    ORDER BY supplier_id DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error loading suppliers: " . mysqli_error($conn));
}


/* =========================================================
   PAGE HEADER
   ========================================================= */

include "../includes/header.php";

?>


<!-- PAGE TITLE -->

<div class="page-title">

    <h2>🏢 Supplier List</h2>

    <a href="add.php" class="btn">
        ＋ Add Supplier
    </a>

</div>


<!-- SUCCESS MESSAGES -->

<?php if (isset($_GET['deleted'])): ?>

    <div class="success">
        🗑️ Supplier deleted successfully.
    </div>

<?php endif; ?>


<?php if (isset($_GET['updated'])): ?>

    <div class="success">
        ✅ Supplier updated successfully.
    </div>

<?php endif; ?>


<?php if (isset($_GET['added'])): ?>

    <div class="success">
        ✅ Supplier added successfully.
    </div>

<?php endif; ?>


<!-- SUPPLIER TABLE -->

<?php if (mysqli_num_rows($result) > 0): ?>

<div class="table-container">

    <div class="product-table-card">

        <div class="product-table-wrapper">

            <table class="product-table">

                <thead>

                    <tr>

                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                /*
                 * Display number only.
                 * This is NOT the database supplier_id.
                 */

                $no = 1;

                while ($row = mysqli_fetch_assoc($result)):

                ?>

                    <tr>

                        <!-- DISPLAY NUMBER -->

                        <td>

                            <strong>
                                <?php echo $no; ?>
                            </strong>

                        </td>


                        <!-- NAME -->

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $row['name']
                                );
                                ?>
                            </strong>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['email']
                            );
                            ?>

                        </td>


                        <!-- PHONE -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['phone']
                            );
                            ?>

                        </td>


                        <!-- ADDRESS -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['address']
                            );
                            ?>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <div class="actions">

                                <a
                                    href="edit.php?id=<?php echo (int) $row['supplier_id']; ?>"
                                    class="btn action-btn edit-btn"
                                >
                                    ✏️ Edit
                                </a>


                                <a
                                    href="delete.php?id=<?php echo (int) $row['supplier_id']; ?>"
                                    class="btn action-btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this supplier?');"
                                >
                                    🗑️ Delete
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php

                    /*
                     * Increase visible number.
                     */

                    $no++;

                endwhile;

                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- RESULT COUNT -->

<div class="result-count">

    Showing

    <strong>
        <?php echo $no - 1; ?>
    </strong>

    supplier(s)

</div>


<?php else: ?>


<!-- EMPTY STATE -->

<div class="empty-state">

    <div class="empty-icon">
        🏢
    </div>

    <h3>
        No Suppliers Found
    </h3>

    <p>
        There are currently no suppliers in your inventory.
    </p>

    <a href="add.php" class="btn">
        ＋ Add First Supplier
    </a>

</div>

<?php endif; ?>


<?php

include "../includes/footer.php";

?>
