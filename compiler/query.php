<?php

session_start();

require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/lexer.php";
require_once __DIR__ . "/parser.php";
require_once __DIR__ . "/semantic.php";
require_once __DIR__ . "/errors.php";

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Query State
|--------------------------------------------------------------------------
*/

$query = "";
$tokens = [];
$parseTree = null;
$results = [];
$error = "";
$stage = "";

/*
|--------------------------------------------------------------------------
| Process Query
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $query = trim($_POST['query'] ?? "");

    if ($query === "") {

        $error = "Please enter an inventory query.";
        $stage = "INPUT";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Lexical Analysis
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | 2. Syntax Analysis
            |--------------------------------------------------------------------------
            */

            $parser = new Parser($tokens);
            $parseTree = $parser->parse();

            /*
            |--------------------------------------------------------------------------
            | 3. Semantic Analysis
            |--------------------------------------------------------------------------
            */

            $semanticAnalyzer = new SemanticAnalyzer();
            $semanticAnalyzer->analyze($parseTree);

            /*
            |--------------------------------------------------------------------------
            | 4. SQL Generation
            |--------------------------------------------------------------------------
            */

            $condition = $parseTree['condition'];

            $allowedFields = [
                'product_id',
                'name',
                'description',
                'price',
                'quantity',
                'supplier_id',
                'image'
            ];

            /*
            |--------------------------------------------------------------------------
            | Build SQL condition recursively
            |--------------------------------------------------------------------------
            */

            $buildCondition = function ($condition) use (
                &$buildCondition,
                $conn,
                $allowedFields
            ) {

                /*
                |--------------------------------------------------------------------------
                | Simple expression
                |--------------------------------------------------------------------------
                */

                if ($condition['type'] === 'EXPRESSION') {

                    $field = strtolower($condition['field']);

                    if (!in_array($field, $allowedFields, true)) {

                        throw new Exception(
                            "Database Error: Field '{$field}' is not allowed."
                        );
                    }

                    $operator = $condition['operator'];
                    $value = $condition['value'];

                    /*
                    |--------------------------------------------------------------------------
                    | Numeric value
                    |--------------------------------------------------------------------------
                    */

                    if (is_numeric($value)) {

                        return "`{$field}` {$operator} " . (float)$value;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | String value
                    |--------------------------------------------------------------------------
                    */

                    $escapedValue = mysqli_real_escape_string(
                        $conn,
                        $value
                    );

                    return "`{$field}` {$operator} '{$escapedValue}'";
                }

                /*
                |--------------------------------------------------------------------------
                | Logical expression
                |--------------------------------------------------------------------------
                */

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

                    return "({$left} {$operator} {$right})";
                }

                throw new Exception(
                    "Database Error: Invalid condition."
                );
            };

            $whereClause = $buildCondition($condition);

            /*
            |--------------------------------------------------------------------------
            | 5. Database Execution
            |--------------------------------------------------------------------------
            */

            $sql = "
                SELECT
                    p.*,
                    s.name AS supplier_name
                FROM products p
                LEFT JOIN suppliers s
                    ON p.supplier_id = s.supplier_id
                WHERE {$whereClause}
                ORDER BY p.product_id DESC
            ";

            $result = mysqli_query($conn, $sql);

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
            |--------------------------------------------------------------------------
            | Determine Compiler Stage
            |--------------------------------------------------------------------------
            */

            if (stripos($error, "Lexical Error") === 0) {

                $stage = "LEXICAL ANALYSIS";

            } elseif (stripos($error, "Syntax Error") === 0) {

                $stage = "SYNTAX ANALYSIS";

            } elseif (stripos($error, "Semantic Error") === 0) {

                $stage = "SEMANTIC ANALYSIS";

            } elseif (stripos($error, "Database Error") === 0) {

                $stage = "DATABASE";

            } elseif ($stage === "") {

                $stage = "COMPILER";
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Helper: Escape HTML
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| Page Header
|--------------------------------------------------------------------------
*/

include "../includes/header.php";

?>

<!-- =========================================================
     PAGE HEADER
========================================================= -->

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
                    required
                ><?php echo e($query); ?></textarea>

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
        ================================================== -->

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

        <p>
            <strong>
                Stage:
            </strong>

            <?php echo e($stage); ?>
        </p>

    <?php endif; ?>

    <p>
        <?php echo e($error); ?>
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
                                <?php echo e($token['type']); ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo e($token['value']); ?>
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
     SUCCESS + PARSE TREE + RESULTS
========================================================= -->

<?php if ($parseTree !== null && $error === ""): ?>

<!-- =========================================================
     COMPILER CHECKS
========================================================= -->

<div class="compiler-success">

    <h3>✅ Compiler Checks Passed</h3>

    <p>
        Lexical Analysis:
        <strong>VALID</strong>
    </p>

    <p>
        Syntax Analysis:
        <strong>VALID</strong>
    </p>

    <p>
        Semantic Analysis:
        <strong>VALID</strong>
    </p>

</div>


<!-- =========================================================
     SYNTAX ANALYSIS
========================================================= -->

<div class="product-table-card compiler-result-card">

    <div class="query-analyzer-content">

        <h3>2. Syntax Analysis</h3>

        <p>
            The query was successfully parsed using
            <strong>Recursive Descent Parsing</strong>.
        </p>

        <pre class="parse-tree"><?php
echo e(
    print_r($parseTree, true)
);
?></pre>

    </div>

</div>


<!-- =========================================================
     SEMANTIC ANALYSIS + INVENTORY RESULTS
========================================================= -->

<div class="product-table-card compiler-result-card">

    <div class="query-analyzer-content">

        <h3>3. Semantic Analysis</h3>

        <p>
            The query passed symbol-table, operator,
            and type checking.
        </p>

        <div class="compiler-status-line">
            <span>Semantic Analysis</span>
            <strong>VALID</strong>
        </div>

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
                            $statusClass = "available";

                        } elseif ($quantity > 0) {

                            $status = "Low Stock";
                            $statusClass = "low";

                        } else {

                            $status = "Out of Stock";
                            $statusClass = "out";
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
                                echo e($row['name']);
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
                                <?php echo $quantity; ?>
                            </td>

                            <td>
                                <?php
                                echo e(
                                    $row['supplier_name']
                                    ?? "No Supplier"
                                );
                                ?>
                            </td>

                            <td>

                                <span
                                    class="status <?php echo e($statusClass); ?>"
                                >
                                    <?php echo e($status); ?>
                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <div class="query-result-summary">

                <strong>
                    <?php echo count($results); ?>
                </strong>

                matching product<?php echo count($results) === 1 ? '' : 's'; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">

                <h3>🔍 No Matching Products</h3>

                <p>
                    The query is valid, but no products
                    matched the specified condition.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php endif; ?>


<?php include "../includes/footer.php"; ?>
