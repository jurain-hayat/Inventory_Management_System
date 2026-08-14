<?php

session_start();

require_once "../db.php";
require_once "lexer.php";
require_once "parser.php";
require_once "semantic.php";

/*
 * =========================================================
 * LOGIN CHECK
 * =========================================================
 */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}


/*
 * =========================================================
 * QUERY VARIABLES
 * =========================================================
 */

$query = "";
$tokens = [];
$parseTree = null;
$results = [];
$error = "";
$stage = "";


/*
 * =========================================================
 * PROCESS QUERY
 * =========================================================
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $query = trim($_POST['query'] ?? "");

    if ($query === "") {

        $error = "Please enter an inventory query.";
        $stage = "INPUT";

    } else {

        try {

            /*
             * =================================================
             * 1. LEXICAL ANALYSIS
             * =================================================
             */

            $tokens = tokenize($query);

            foreach ($tokens as $token) {

                if ($token['type'] === 'INVALID') {

                    throw new Exception(
                        "Lexical Error: Invalid character '{$token['value']}'."
                    );
                }
            }


            /*
             * =================================================
             * 2. SYNTAX ANALYSIS
             * =================================================
             */

            $parser = new Parser($tokens);

            $parseTree = $parser->parse();


            /*
             * =================================================
             * 3. SEMANTIC ANALYSIS
             * =================================================
             */

            $semanticAnalyzer = new SemanticAnalyzer();

            $semanticAnalyzer->analyze($parseTree);


            /*
             * =================================================
             * 4. DATABASE QUERY
             *
             * The compiler has now validated the query.
             * We convert the validated parse tree into
             * a safe SQL query.
             * =================================================
             */

            $condition = $parseTree['condition'];

            /*
             * Build SQL WHERE clause recursively.
             */

            $buildCondition = function ($condition) use (&$buildCondition, $conn) {

                if ($condition['type'] === 'EXPRESSION') {

                    $allowedFields = [
                        'product_id',
                        'name',
                        'description',
                        'price',
                        'quantity',
                        'supplier_id',
                        'image'
                    ];

                    $field = strtolower($condition['field']);

                    if (!in_array($field, $allowedFields, true)) {
                        throw new Exception(
                            "Database Error: Field '{$field}' is not allowed."
                        );
                    }

                    $operator = $condition['operator'];
                    $value = $condition['value'];

                    /*
                     * Numeric values
                     */

                    if (is_numeric($value)) {

                        return "`$field` $operator " . (float)$value;
                    }

                    /*
                     * String values
                     */

                    $escapedValue = mysqli_real_escape_string(
                        $conn,
                        $value
                    );

                    return "`$field` $operator '{$escapedValue}'";
                }


                if ($condition['type'] === 'LOGICAL') {

                    $left = $buildCondition(
                        $condition['left']
                    );

                    $right = $buildCondition(
                        $condition['right']
                    );

                    $operator = strtoupper(
                        $condition['operator']
                    );

                    return "($left $operator $right)";
                }


                throw new Exception(
                    "Database Error: Invalid condition."
                );
            };


            $whereClause = $buildCondition(
                $condition
            );


            /*
             * =================================================
             * EXECUTE DATABASE QUERY
             * =================================================
             */

            $sql = "
                SELECT
                    p.*,
                    s.name AS supplier_name
                FROM products p
                LEFT JOIN suppliers s
                    ON p.supplier_id = s.supplier_id
                WHERE $whereClause
                ORDER BY p.product_id DESC
            ";


            $result = mysqli_query(
                $conn,
                $sql
            );


            if (!$result) {

                throw new Exception(
                    "Database Error: " . mysqli_error($conn)
                );
            }


            while ($row = mysqli_fetch_assoc($result)) {

                $results[] = $row;
            }


        } catch (Exception $e) {

            $error = $e->getMessage();

            /*
             * Determine which compiler stage failed.
             */

            if (stripos($error, "Lexical Error") === 0) {

                $stage = "LEXICAL ANALYSIS";

            } elseif (stripos($error, "Syntax Error") === 0) {

                $stage = "SYNTAX ANALYSIS";

            } elseif (stripos($error, "Semantic Error") === 0) {

                $stage = "SEMANTIC ANALYSIS";

            } elseif (stripos($error, "Database Error") === 0) {

                $stage = "DATABASE";

            } else {

                $stage = "COMPILER";
            }
        }
    }
}


/*
 * =========================================================
 * PAGE HEADER
 * =========================================================
 */

include "../includes/header.php";

?>

<div class="page-title">

    <div class="product-page-title">

        <h2>🔎 Inventory Query Analyzer</h2>

        <p>
            Execute inventory queries through the compiler pipeline.
        </p>

    </div>

</div>


<!-- =========================================================
     QUERY INPUT
     ========================================================= -->

<div class="product-table-card query-analyzer-card">

    <div class="query-analyzer-content">

        <h3>Inventory Query</h3>

        <p>
            Enter a query using the Inventory Query Language.
        </p>


        <form method="POST">

            <div class="search-field">

                <label
                    for="query"
                    class="form-label"
                >
                    Query
                </label>

                <textarea
                    id="query"
                    name="query"
                    rows="4"
                    placeholder="SHOW PRODUCTS WHERE quantity < 10"
                ><?php echo htmlspecialchars($query); ?></textarea>

            </div>


            <button
                type="submit"
                class="btn search-btn"
            >
                ▶ Analyze Query
            </button>

        </form>


        <!-- =================================================
             EXAMPLE QUERIES
             ================================================= -->

        <div class="query-examples">

            <strong>Example queries:</strong>

            <code>
                SHOW PRODUCTS WHERE quantity &lt; 10
            </code>

            <code>
                SHOW PRODUCTS WHERE price &gt; 1000
            </code>

            <code>
                SHOW PRODUCTS WHERE quantity &lt;= 5
            </code>

            <code>
                SHOW PRODUCTS WHERE quantity &lt; 10 AND price &gt; 1000
            </code>

        </div>

    </div>

</div>


<!-- =========================================================
     ERROR
     ========================================================= -->

<?php if ($error !== ""): ?>

<div class="compiler-error">

    <h3>❌ Compiler Error</h3>

    <?php if ($stage !== ""): ?>

        <strong>
            Stage: <?php echo htmlspecialchars($stage); ?>
        </strong>

    <?php endif; ?>

    <p>
        <?php echo htmlspecialchars($error); ?>
    </p>

</div>

<?php endif; ?>


<!-- =========================================================
     LEXICAL ANALYSIS
     ========================================================= -->

<?php if (!empty($tokens)): ?>

<div class="product-table-card compiler-result-card">

    <div class="query-analyzer-content">

        <h3>1. Lexical Analysis</h3>

        <p>
            The query was converted into tokens.
        </p>


        <div class="compiler-table-wrapper">

            <table class="product-table">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Token Type</th>
                        <th>Value</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($tokens as $index => $token): ?>

                    <tr>

                        <td>
                            <?php echo $index + 1; ?>
                        </td>

                        <td>
                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $token['type']
                                );
                                ?>
                            </strong>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $token['value']
                            );
                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php endif; ?>


<!-- =========================================================
     SYNTAX + SEMANTIC SUCCESS
     ========================================================= -->

<?php if ($parseTree !== null && $error === ""): ?>

<div class="compiler-success">

    <h3>✅ Compiler Checks Passed</h3>

    <p>
        Lexical Analysis: <strong>VALID</strong>
    </p>

    <p>
        Syntax Analysis: <strong>VALID</strong>
    </p>

    <p>
        Semantic Analysis: <strong>VALID</strong>
    </p>

</div>


<!-- =========================================================
     PARSE TREE
     ========================================================= -->

<div class="product-table-card compiler-result-card">

    <div class="query-analyzer-content">

        <h3>2. Syntax Analysis</h3>

        <p>
            The query was successfully parsed using
            Recursive Descent Parsing.
        </p>

        <pre class="parse-tree"><?php
echo htmlspecialchars(
    print_r($parseTree, true)
);
?></pre>

    </div>

</div>


<!-- =========================================================
     DATABASE RESULTS
     ========================================================= -->

<div class="product-table-card compiler-result-card">

    <div class="query-analyzer-content">

        <h3>3. Semantic Analysis</h3>

        <p>
            The query passed symbol-table, operator,
            and type checking.
        </p>

        <h3>4. Inventory Results</h3>

        <?php if (!empty($results)): ?>

        <div class="compiler-table-wrapper">

            <table class="product-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Supplier</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($results as $row): ?>

                    <?php

                    $quantity = (int)$row['quantity'];

                    if ($quantity > 5) {

                        $status = "Available";

                    } elseif ($quantity > 0) {

                        $status = "Low Stock";

                    } else {

                        $status = "Out of Stock";
                    }

                    ?>

                    <tr>

                        <td>
                            <?php
                            echo (int)$row['product_id'];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['name']
                            );
                            ?>
                        </td>

                        <td>
                            ৳<?php
                            echo number_format(
                                (float)$row['price'],
                                2
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo $quantity;
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['supplier_name']
                                ?? "No Supplier"
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo $status;
                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php else: ?>

            <div class="empty-state">

                <h3>No matching products</h3>

                <p>
                    The query is valid, but no products
                    matched the condition.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php endif; ?>


<?php include "../includes/footer.php"; ?>