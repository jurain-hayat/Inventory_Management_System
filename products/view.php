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
   PAGINATION
   ========================================================= */

$limit = 10;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$offset = ($page - 1) * $limit;

/* =========================================================
   SEARCH
   ========================================================= */

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : "";

/* =========================================================
   SORTING
   ========================================================= */

$allowedSorts = [
    "newest",
    "oldest",
    "name_asc",
    "name_desc",
    "price_asc",
    "price_desc",
    "qty_asc",
    "qty_desc"
];

$sort = (
    isset($_GET['sort']) &&
    in_array($_GET['sort'], $allowedSorts, true)
)
    ? $_GET['sort']
    : "newest";

/* =========================================================
   ORDER BY
   ========================================================= */

switch ($sort) {

    case "oldest":
        $orderBy = "p.product_id ASC";
        break;

    case "name_asc":
        $orderBy = "p.name ASC, p.product_id ASC";
        break;

    case "name_desc":
        $orderBy = "p.name DESC, p.product_id DESC";
        break;

    case "price_asc":
        $orderBy = "p.price ASC, p.product_id ASC";
        break;

    case "price_desc":
        $orderBy = "p.price DESC, p.product_id DESC";
        break;

    case "qty_asc":
        $orderBy = "p.quantity ASC, p.product_id ASC";
        break;

    case "qty_desc":
        $orderBy = "p.quantity DESC, p.product_id DESC";
        break;

    default:
        $orderBy = "p.product_id DESC";
        break;
}

/* =========================================================
   COUNT PRODUCTS
   ========================================================= */

$totalRecords = 0;

if ($search !== "") {

    $keyword = "%{$search}%";

    $countStmt = mysqli_prepare(
        $conn,
        "SELECT COUNT(*)
         FROM products p
         LEFT JOIN suppliers s
            ON p.supplier_id = s.supplier_id
         WHERE p.name LIKE ?
            OR p.description LIKE ?
            OR s.name LIKE ?"
    );

    mysqli_stmt_bind_param(
        $countStmt,
        "sss",
        $keyword,
        $keyword,
        $keyword
    );

} else {

    $countStmt = mysqli_prepare(
        $conn,
        "SELECT COUNT(*)
         FROM products"
    );
}

mysqli_stmt_execute($countStmt);

mysqli_stmt_bind_result(
    $countStmt,
    $totalRecords
);

mysqli_stmt_fetch($countStmt);

mysqli_stmt_close($countStmt);

/* =========================================================
   TOTAL PAGES
   ========================================================= */

$totalPages = max(
    1,
    (int) ceil($totalRecords / $limit)
);

/* If current page no longer exists after deletion */
if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $limit;

/* =========================================================
   GET PRODUCTS
   ========================================================= */

if ($search !== "") {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            p.*,
            s.name AS supplier_name
         FROM products p
         LEFT JOIN suppliers s
            ON p.supplier_id = s.supplier_id
         WHERE p.name LIKE ?
            OR p.description LIKE ?
            OR s.name LIKE ?
         ORDER BY $orderBy
         LIMIT ?, ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssii",
        $keyword,
        $keyword,
        $keyword,
        $offset,
        $limit
    );

} else {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            p.*,
            s.name AS supplier_name
         FROM products p
         LEFT JOIN suppliers s
            ON p.supplier_id = s.supplier_id
         ORDER BY $orderBy
         LIMIT ?, ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $offset,
        $limit
    );
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

mysqli_stmt_close($stmt);

/* =========================================================
   PAGE HEADER
   ========================================================= */

include "../includes/header.php";

?>

<!-- =========================================================
     PAGE TITLE
     ========================================================= -->

<div class="page-title">

    <div class="product-page-title">
        <h2>📦 Product List</h2>
    </div>

    <a
        href="add.php"
        class="btn add-product-btn"
    >
        ＋ Add Product
    </a>

</div>


<!-- =========================================================
     SUCCESS MESSAGES
     ========================================================= -->

<?php if (isset($_GET['added'])): ?>

    <div class="success">
        <span>✅</span>
        <span>Product added successfully.</span>
    </div>

<?php endif; ?>


<?php if (isset($_GET['updated'])): ?>

    <div class="success">
        <span>✅</span>
        <span>Product updated successfully.</span>
    </div>

<?php endif; ?>


<?php if (isset($_GET['deleted'])): ?>

    <div class="success">
        <span>🗑️</span>
        <span>Product deleted successfully.</span>
    </div>

<?php endif; ?>


<!-- =========================================================
     SEARCH / SORT TOOLBAR
     ========================================================= -->

<div class="product-toolbar">

    <form
        method="GET"
        class="product-search"
    >

        <!-- SEARCH -->

        <div class="search-field">

            <label
                for="search"
                class="form-label"
            >
                Search Product
            </label>

            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search by product, description or supplier..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

        </div>


        <!-- SORT -->

        <div class="sort-field">

            <label
                for="sort"
                class="form-label"
            >
                Sort By
            </label>

            <select
                id="sort"
                name="sort"
            >

                <option
                    value="newest"
                    <?php echo $sort === "newest" ? "selected" : ""; ?>
                >
                    Newest
                </option>

                <option
                    value="oldest"
                    <?php echo $sort === "oldest" ? "selected" : ""; ?>
                >
                    Oldest
                </option>

                <option
                    value="name_asc"
                    <?php echo $sort === "name_asc" ? "selected" : ""; ?>
                >
                    Name A-Z
                </option>

                <option
                    value="name_desc"
                    <?php echo $sort === "name_desc" ? "selected" : ""; ?>
                >
                    Name Z-A
                </option>

                <option
                    value="price_asc"
                    <?php echo $sort === "price_asc" ? "selected" : ""; ?>
                >
                    Price Low → High
                </option>

                <option
                    value="price_desc"
                    <?php echo $sort === "price_desc" ? "selected" : ""; ?>
                >
                    Price High → Low
                </option>

                <option
                    value="qty_asc"
                    <?php echo $sort === "qty_asc" ? "selected" : ""; ?>
                >
                    Quantity Low → High
                </option>

                <option
                    value="qty_desc"
                    <?php echo $sort === "qty_desc" ? "selected" : ""; ?>
                    >
                    Quantity High → Low
                </option>

            </select>

        </div>


        <!-- SEARCH BUTTON -->

        <button
            type="submit"
            class="btn search-btn"
        >
            🔍 Search
        </button>

    </form>

</div>


<!-- =========================================================
     SEARCH INFORMATION
     ========================================================= -->

<div class="search-info">

    <div>

        <?php if ($search !== ""): ?>

            Search results for:

            <strong>
                "<?php echo htmlspecialchars($search); ?>"
            </strong>

        <?php else: ?>

            Showing all products

        <?php endif; ?>

    </div>


    <div>

        Total Products:

        <strong>
            <?php echo (int) $totalRecords; ?>
        </strong>

    </div>

</div>


<!-- =========================================================
     PRODUCT TABLE
     ========================================================= -->

<?php if ($totalRecords > 0): ?>

<div class="table-container">

    <div class="product-table-card">

        <div class="product-table-wrapper">

            <table class="product-table">

                <thead>

                    <tr>

                        <!-- DISPLAY NUMBER ONLY -->
                        <th>No.</th>

                        <th>Image</th>

                        <th>Name</th>

                        <th>Description</th>

                        <th>Price</th>

                        <th>Quantity</th>

                        <th>Supplier</th>

                        <th>Status</th>

                        <th>Created</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                /*
                 * Display number starts from 1.
                 * On page 2 it starts from 11.
                 * The actual database product_id is NOT displayed.
                 */

                $displayNumber = $offset + 1;

                ?>


                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <?php

                    /* =================================================
                       STOCK STATUS
                       ================================================= */

                    $quantity = (int) $row['quantity'];

                    if ($quantity > 5) {

                        $statusClass = "available";
                        $statusText = "Available";

                    } elseif ($quantity > 0) {

                        $statusClass = "low";
                        $statusText = "Low Stock";

                    } else {

                        $statusClass = "out";
                        $statusText = "Out of Stock";

                    }


                    /* =================================================
                       IMAGE
                       ================================================= */

                    $image = trim(
                        $row['image'] ?? ""
                    );

                    ?>

                    <tr>

                        <!-- =================================================
                             DISPLAY NUMBER
                             ================================================= -->

                        <td>

                            <strong class="product-id">

                                <?php
                                echo $displayNumber;
                                ?>

                            </strong>

                        </td>


                        <!-- =================================================
                             IMAGE
                             ================================================= -->

                        <td>

                            <?php if ($image !== ""): ?>

                                <img
                                    src="../assets/uploads/<?php echo htmlspecialchars($image); ?>"
                                    alt="<?php echo htmlspecialchars($row['name']); ?>"
                                    class="product-image"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <div
                                    class="no-image"
                                    style="display:none;"
                                >
                                    NO IMAGE
                                </div>

                            <?php else: ?>

                                <div class="no-image">
                                    NO IMAGE
                                </div>

                            <?php endif; ?>

                        </td>


                        <!-- =================================================
                             NAME
                             ================================================= -->

                        <td>

                            <div class="product-name">

                                <?php
                                echo htmlspecialchars(
                                    $row['name']
                                );
                                ?>

                            </div>

                        </td>


                        <!-- =================================================
                             DESCRIPTION
                             ================================================= -->

                        <td>

                            <div class="product-description">

                                <?php

                                $description = trim(
                                    $row['description'] ?? ""
                                );

                                if ($description === "") {

                                    echo "No description";

                                } else {

                                    echo htmlspecialchars(
                                        $description
                                    );

                                }

                                ?>

                            </div>

                        </td>


                        <!-- =================================================
                             PRICE
                             ================================================= -->

                        <td>

                            <span class="price">

                                ৳<?php

                                echo number_format(
                                    (float) $row['price'],
                                    2
                                );

                                ?>

                            </span>

                        </td>


                        <!-- =================================================
                             QUANTITY
                             ================================================= -->

                        <td>

                            <span
                                class="quantity
                                <?php

                                if ($quantity === 0) {

                                    echo " quantity-out";

                                } elseif ($quantity <= 5) {

                                    echo " quantity-low";

                                }

                                ?>"
                            >

                                <?php
                                echo $quantity;
                                ?>

                            </span>

                        </td>


                        <!-- =================================================
                             SUPPLIER
                             ================================================= -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['supplier_name']
                                ?? "No Supplier"
                            );

                            ?>

                        </td>


                        <!-- =================================================
                             STATUS
                             ================================================= -->

                        <td>

                            <span
                                class="status <?php echo $statusClass; ?>"
                            >

                                <?php
                                echo $statusText;
                                ?>

                            </span>

                        </td>


                        <!-- =================================================
                             CREATED
                             ================================================= -->

                        <td>

                            <span class="created-date">

                                <?php

                                echo htmlspecialchars(
                                    $row['created_at']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- =================================================
                             ACTION
                             ================================================= -->

                        <td>

                            <div class="actions">

                                <!--
                                    Real database ID is used here,
                                    but NOT shown to the user.
                                -->

                                <a
                                    href="edit.php?id=<?php echo (int) $row['product_id']; ?>"
                                    class="btn action-btn edit-btn"
                                >
                                    ✏️ Edit
                                </a>


                                <a
                                    href="delete.php?id=<?php echo (int) $row['product_id']; ?>"
                                    class="btn action-btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this product?');"
                                >
                                    🗑️ Delete
                                </a>

                            </div>

                        </td>

                    </tr>


                    <?php

                    /*
                     * Increase only the DISPLAY number.
                     * Database product_id remains unchanged.
                     */

                    $displayNumber++;

                    ?>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<?php else: ?>


<!-- =========================================================
     EMPTY STATE
     ========================================================= -->

<div class="empty-state">

    <div class="empty-icon">
        📦
    </div>

    <h3>
        No Products Found
    </h3>

    <p>

        <?php if ($search !== ""): ?>

            No products matched

            "<strong>
                <?php echo htmlspecialchars($search); ?>
            </strong>".

        <?php else: ?>

            There are currently no products in your inventory.

        <?php endif; ?>

    </p>


    <?php if ($search !== ""): ?>

        <a
            href="view.php"
            class="btn"
        >
            ✕ Clear Search
        </a>

    <?php else: ?>

        <a
            href="add.php"
            class="btn"
        >
            ＋ Add First Product
        </a>

    <?php endif; ?>

</div>

<?php endif; ?>


<!-- =========================================================
     RESULT COUNT
     ========================================================= -->

<?php if ($totalRecords > 0): ?>

    <?php

    $start = $offset + 1;

    $end = min(
        $offset + $limit,
        $totalRecords
    );

    ?>

    <div class="result-count">

        Showing

        <strong>
            <?php echo $start; ?>
        </strong>

        -

        <strong>
            <?php echo $end; ?>
        </strong>

        of

        <strong>
            <?php echo $totalRecords; ?>
        </strong>

        products

    </div>

<?php endif; ?>


<!-- =========================================================
     PAGINATION
     ========================================================= -->

<?php if ($totalPages > 1): ?>

<div class="pagination">

    <!-- PREVIOUS -->

    <?php if ($page > 1): ?>

        <a
            href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>"
            aria-label="Previous page"
        >
            ←
        </a>

    <?php endif; ?>


    <?php

    $startPage = max(
        1,
        $page - 2
    );

    $endPage = min(
        $totalPages,
        $page + 2
    );

    ?>


    <!-- FIRST PAGE -->

    <?php if ($startPage > 1): ?>

        <a
            href="?page=1&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>"
        >
            1
        </a>

        <?php if ($startPage > 2): ?>

            <span class="pagination-dots">
                ...
            </span>

        <?php endif; ?>

    <?php endif; ?>


    <!-- PAGE NUMBERS -->

    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

        <?php if ($i == $page): ?>

            <strong>
                <?php echo $i; ?>
            </strong>

        <?php else: ?>

            <a
                href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>"
            >
                <?php echo $i; ?>
            </a>

        <?php endif; ?>

    <?php endfor; ?>


    <!-- LAST PAGE -->

    <?php if ($endPage < $totalPages): ?>

        <?php if ($endPage < $totalPages - 1): ?>

            <span class="pagination-dots">
                ...
            </span>

        <?php endif; ?>

        <a
            href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>"
        >
            <?php echo $totalPages; ?>
        </a>

    <?php endif; ?>


    <!-- NEXT -->

    <?php if ($page < $totalPages): ?>

        <a
            href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>"
            aria-label="Next page"
        >
            →
        </a>

    <?php endif; ?>

</div>

<?php endif; ?>


<?php

include "../includes/footer.php";

?>