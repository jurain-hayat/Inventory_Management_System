<?php

require_once __DIR__ . '/lexer.php';
require_once __DIR__ . '/parser.php';
require_once __DIR__ . '/semantic.php';

/**
 * Test a compiler error.
 */
function testError(string $label, string $query): void
{
    echo $label . ":\n";
    echo "Query: " . $query . "\n";
    echo "-----------------------------\n";

    try {

        // ==========================================
        // 1. LEXICAL ANALYSIS
        // ==========================================

        $tokens = tokenize($query);

        foreach ($tokens as $token) {

            if ($token['type'] === 'INVALID') {
                throw new Exception(
                    "Lexical Error: Invalid character '{$token['value']}'."
                );
            }
        }

        // ==========================================
        // 2. SYNTAX ANALYSIS
        // ==========================================

        $parser = new Parser($tokens);

        $parseTree = $parser->parse();

        // ==========================================
        // 3. SEMANTIC ANALYSIS
        // ==========================================

        $semantic = new SemanticAnalyzer();

        $semantic->analyze($parseTree);

        echo "ERROR: Query was accepted unexpectedly.\n\n";

    } catch (Exception $e) {

        echo $e->getMessage() . "\n\n";
    }
}


// ==========================================
// TEST 1 — LEXICAL ERROR
// ==========================================

testError(
    "TEST 1 — LEXICAL ERROR",
    "SHOW PRODUCTS WHERE quantity @ 10"
);


// ==========================================
// TEST 2 — SYNTAX ERROR
// ==========================================

testError(
    "TEST 2 — SYNTAX ERROR",
    "SHOW PRODUCTS WHERE quantity <"
);


// ==========================================
// TEST 3 — SEMANTIC ERROR: UNKNOWN FIELD
// ==========================================

testError(
    "TEST 3 — UNKNOWN FIELD",
    "SHOW PRODUCTS WHERE abc < 10"
);


// ==========================================
// TEST 4 — SEMANTIC ERROR: INVALID OPERATOR
// ==========================================

testError(
    "TEST 4 — INVALID OPERATOR",
    "SHOW PRODUCTS WHERE name > 100"
);


// ==========================================
// TEST 5 — SEMANTIC ERROR: WRONG TYPE
// ==========================================

testError(
    "TEST 5 — WRONG VALUE TYPE",
    "SHOW PRODUCTS WHERE quantity < abc"
);