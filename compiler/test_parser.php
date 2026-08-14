<?php

require_once __DIR__ . '/lexer.php';
require_once __DIR__ . '/parser.php';
require_once __DIR__ . '/semantic.php';

$query = "SHOW PRODUCTS WHERE quantity < 5 OR price > 500";

echo "Query:\n";
echo $query . "\n\n";

try {

    // ==========================================
    // STEP 1 — LEXICAL ANALYSIS
    // ==========================================

    $tokens = tokenize($query);

    echo "LEXICAL ANALYSIS:\n";
    echo "-----------------------------\n";

    foreach ($tokens as $token) {
        echo $token['type'] . " => " . $token['value'] . "\n";
    }

    echo "\n";

    // ==========================================
    // STEP 2 — SYNTAX ANALYSIS
    // ==========================================

    $parser = new Parser($tokens);

    $parseTree = $parser->parse();

    echo "SYNTAX ANALYSIS:\n";
    echo "-----------------------------\n";
    echo "Query is syntactically VALID.\n\n";

    echo "PARSE TREE:\n";
    print_r($parseTree);

    echo "\n";

    // ==========================================
    // STEP 3 — SEMANTIC ANALYSIS
    // ==========================================

    $semantic = new SemanticAnalyzer();

    $semantic->analyze($parseTree);

    echo "SEMANTIC ANALYSIS:\n";
    echo "-----------------------------\n";
    echo "Query is semantically VALID.\n\n";

    echo "ALL COMPILER CHECKS PASSED.\n";

} catch (Exception $e) {

    echo "\nCOMPILER ERROR:\n";
    echo "-----------------------------\n";
    echo $e->getMessage() . "\n";
}