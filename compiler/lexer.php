<?php

/**
 * Inventory Query Lexer
 *
 * Compiler Design Concept:
 * Lexical Analysis
 *
 * Converts an inventory query into tokens.
 */

function tokenize($query)
{
    $tokens = [];

    $position = 0;
    $length = strlen($query);

    while ($position < $length) {

        /*
         * 1. Ignore whitespace
         */
        if (preg_match('/^\s+/', substr($query, $position), $match)) {
            $position += strlen($match[0]);
            continue;
        }

        $remaining = substr($query, $position);

        /*
         * 2. Keywords
         */
        if (preg_match(
            '/^(SHOW|PRODUCTS|SUPPLIERS|SALES|WHERE|AND|OR|LOW|STOCK)\b/i',
            $remaining,
            $match
        )) {
            $tokens[] = [
                'type'  => 'KEYWORD',
                'value' => strtoupper($match[0])
            ];

            $position += strlen($match[0]);
            continue;
        }

        /*
         * 3. Operators
         *
         * Check two-character operators first.
         */
        if (preg_match('/^(<=|>=|!=|=|<|>)/', $remaining, $match)) {
            $tokens[] = [
                'type'  => 'OPERATOR',
                'value' => $match[0]
            ];

            $position += strlen($match[0]);
            continue;
        }

        /*
         * 4. Numbers
         */
        if (preg_match('/^\d+(?:\.\d+)?/', $remaining, $match)) {
            $tokens[] = [
                'type'  => 'NUMBER',
                'value' => $match[0]
            ];

            $position += strlen($match[0]);
            continue;
        }

        /*
         * 5. Identifiers
         *
         * Examples:
         * quantity
         * price
         * name
         * supplier_id
         */
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*/', $remaining, $match)) {
            $tokens[] = [
                'type'  => 'IDENTIFIER',
                'value' => $match[0]
            ];

            $position += strlen($match[0]);
            continue;
        }

        /*
         * 6. Invalid character
         */
        $tokens[] = [
            'type'  => 'INVALID',
            'value' => $query[$position]
        ];

        $position++;
    }

    return $tokens;
}