<?php

/**
 * Inventory Query Language Parser
 *
 * Compiler Design Concept:
 * Syntax Analysis
 *
 * Uses Recursive Descent Parsing.
 */

class Parser
{
    private array $tokens;
    private int $position = 0;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Return the current token.
     */
    private function currentToken(): ?array
    {
        return $this->tokens[$this->position] ?? null;
    }

    /**
     * Check whether the current token
     * matches the expected type/value.
     */
    private function match(string $type, ?string $value = null): bool
    {
        $token = $this->currentToken();

        if ($token === null) {
            return false;
        }

        if ($token['type'] !== $type) {
            return false;
        }

        if ($value !== null && strtoupper($token['value']) !== strtoupper($value)) {
            return false;
        }

        return true;
    }

    /**
     * Consume a token if it matches.
     */
    private function consume(string $type, ?string $value = null): array
    {
        $token = $this->currentToken();

        if (!$this->match($type, $value)) {

            $expected = $value ?? $type;

            $actual = $token
                ? $token['type'] . " (" . $token['value'] . ")"
                : "END OF QUERY";

            throw new Exception(
                "Syntax Error: Expected {$expected}, but found {$actual}."
            );
        }

        $this->position++;

        return $token;
    }

    /**
     * QUERY
     *
     * QUERY → SHOW PRODUCTS WHERE CONDITION
     */
    public function parse(): array
    {
        $this->consume('KEYWORD', 'SHOW');
        $this->consume('KEYWORD', 'PRODUCTS');
        $this->consume('KEYWORD', 'WHERE');

        $condition = $this->parseCondition();

        if ($this->currentToken() !== null) {

            $token = $this->currentToken();

            throw new Exception(
                "Syntax Error: Unexpected token '{$token['value']}'."
            );
        }

        return [
            'type' => 'QUERY',
            'condition' => $condition
        ];
    }

    /**
     * CONDITION
     *
     * CONDITION → EXPRESSION
     *            | EXPRESSION AND CONDITION
     *            | EXPRESSION OR CONDITION
     */
    private function parseCondition(): array
    {
        $left = $this->parseExpression();

        while (
            $this->match('KEYWORD', 'AND') ||
            $this->match('KEYWORD', 'OR')
        ) {
            $operator = $this->consume('KEYWORD');

            $right = $this->parseExpression();

            $left = [
                'type' => 'LOGICAL',
                'operator' => strtoupper($operator['value']),
                'left' => $left,
                'right' => $right
            ];
        }

        return $left;
    }

    /**
     * EXPRESSION
     *
     * EXPRESSION → IDENTIFIER OPERATOR VALUE
     */
    private function parseExpression(): array
    {
        $field = $this->consume('IDENTIFIER');

        $operator = $this->consume('OPERATOR');

        $valueToken = $this->currentToken();

        if ($valueToken === null) {
            throw new Exception(
                "Syntax Error: Expected a value after operator '{$operator['value']}'."
            );
        }

        if (
            $valueToken['type'] !== 'NUMBER' &&
            $valueToken['type'] !== 'IDENTIFIER'
        ) {
            throw new Exception(
                "Syntax Error: Expected NUMBER or IDENTIFIER, but found '{$valueToken['value']}'."
            );
        }

        $this->position++;

        return [
            'type' => 'EXPRESSION',
            'field' => $field['value'],
            'operator' => $operator['value'],
            'value' => $valueToken['value']
        ];
    }
}