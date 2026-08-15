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
   GET SALES
   ========================================================= */

$query = "
    SELECT
        s.sale_id,
        s.quantity,
        s.unit_price,
        s.total_price,
        s.sale_date,
        p.name AS product_name,
        p.quantity AS remaining_stock
    FROM sales s
    INNER JOIN products p
        ON s.product_id = p.product_id
    ORDER BY s.sale_date DESC, s.sale_id DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error loading sales: " . mysqli_error($conn));
}

/* =========================================================
   PAGE HEADER
   ========================================================= */

include "../includes/header.php";

?>

<!-- PAGE TITLE -->

<div class="page-title">

    <h2>💰 Sales History</h2>

    <a href="add.php" class="btn">
        ＋ New Sale
    </a>

</div>


<!-- SUCCESS MESSAGE -->

<?php if (isset($_GET['added'])): ?>

    <div class="success">
        ✅ Sale completed successfully.
    </div>

<?php endif; ?>


<!-- SALES TABLE -->

<?php if (mysqli_num_rows($result) > 0): ?>

<div class="table-container">

    <div class="product-table-card">

        <div class="product-table-wrapper">

            <table class="product-table">

                <thead>

                    <tr>

                        <!-- Visible number, NOT database Sale ID -->

                        <th>No.</th>

                        <th>Product</th>

                        <th>Quantity</th>

                        <th>Unit Price</th>

                        <th>Total Price</th>

                        <th>Sale Date</th>

                        <th>Remaining Stock</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                /*
                 * This number is only for display.
                 * It will always be 1, 2, 3, 4...
                 * regardless of the actual sale_id.
                 */

                $no = 1;

                while ($row = mysqli_fetch_assoc($result)):

                    $remainingStock = (int) $row['remaining_stock'];

                    if ($remainingStock <= 0) {

                        $status = "Out of Stock";
                        $statusClass = "out";

                    } elseif ($remainingStock <= 5) {

                        $status = "Low Stock";
                        $statusClass = "low";

                    } else {

                        $status = "Available";
                        $statusClass = "available";
                    }

                ?>

                    <tr>

                        <!-- DISPLAY NUMBER -->

                        <td>
                            <strong>
                                <?php echo $no; ?>
                            </strong>
                        </td>


                        <!-- PRODUCT -->

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $row['product_name']
                                );
                                ?>
                            </strong>

                        </td>


                        <!-- QUANTITY -->

                        <td>
                            <?php
                            echo (int) $row['quantity'];
                            ?>
                        </td>


                        <!-- UNIT PRICE -->

                        <td>

                            ৳<?php
                            echo number_format(
                                (float) $row['unit_price'],
                                2
                            );
                            ?>

                        </td>


                        <!-- TOTAL PRICE -->

                        <td>

                            <strong>

                                ৳<?php
                                echo number_format(
                                    (float) $row['total_price'],
                                    2
                                );
                                ?>

                            </strong>

                        </td>


                        <!-- SALE DATE -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['sale_date']
                            );
                            ?>

                        </td>


                        <!-- REMAINING STOCK -->

                        <td>

                            <span class="quantity
                                <?php

                                if ($remainingStock <= 0) {

                                    echo " quantity-out";

                                } elseif ($remainingStock <= 5) {

                                    echo " quantity-low";

                                }

                                ?>
                            ">

                                <?php
                                echo $remainingStock;
                                ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <span class="status <?php echo $statusClass; ?>">

                                <?php
                                echo $status;
                                ?>

                            </span>

                        </td>

                    </tr>

                <?php

                    /*
                     * Increase display number.
                     * This does NOT change sale_id.
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

    sale(s)

</div>


<?php else: ?>


<!-- EMPTY STATE -->

<div class="empty-state">

    <div class="empty-icon">
        💰
    </div>

    <h3>
        No Sales Found
    </h3>

    <p>
        There are currently no sales in your inventory.
    </p>

    <a href="add.php" class="btn">
        ＋ Make First Sale
    </a>

</div>

<?php endif; ?>


<?php

include "../includes/footer.php";

?>

