<?php

require_once __DIR__ . '/lexer.php';

$query = "SHOW PRODUCTS WHERE quantity @ 10";

echo "Query: $query\n\n";

$tokens = tokenize($query);

echo "TOKENS:\n";
echo "-----------------------------\n";

foreach ($tokens as $token) {
    echo $token['type'] . " => " . $token['value'] . "\n";
}