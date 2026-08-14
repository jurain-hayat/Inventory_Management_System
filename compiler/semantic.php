<?php

/**
 * Inventory Query Language
 *
 * Compiler Design Concept:
 * Semantic Analysis
 *
 * Checks:
 * 1. Whether the field exists
 * 2. Whether the operator is valid for the field
 * 3. Whether the value has the correct type
 */

class SemanticAnalyzer
{
    /*
     * Symbol table for the products table.
     *
     * field => data type
     */
    private array $symbolTable = [
        'product_id' => 'NUMBER',
        'name' => 'STRING',
        'description' => 'STRING',
        'price' => 'NUMBER',
        'quantity' => 'NUMBER',
        'supplier_id' => 'NUMBER',
        'image' => 'STRING',
    ];

    /*
     * Operators allowed for each data type.
     */
    private array $operators = [
        'NUMBER' => ['=', '!=', '<', '>', '<=', '>='],
        'STRING' => ['=', '!='],
    ];

    /**
     * Analyze the complete parse tree.
     */
    public function analyze(array $parseTree): bool
    {
        if (
            !isset($parseTree['type']) ||
            $parseTree['type'] !== 'QUERY'
        ) {
            throw new Exception(
                'Semantic Error: Invalid query structure.'
            );
        }

        $this->analyzeCondition($parseTree['condition']);

        return true;
    }

    /**
     * Analyze a condition.
     */
    private function analyzeCondition(array $condition): void
    {
        if ($condition['type'] === 'EXPRESSION') {
            $this->analyzeExpression($condition);
            return;
        }

        if ($condition['type'] === 'LOGICAL') {

            $this->analyzeCondition($condition['left']);
            $this->analyzeCondition($condition['right']);

            return;
        }

        throw new Exception(
            'Semantic Error: Unknown condition type.'
        );
    }

    /**
     * Analyze:
     *
     * IDENTIFIER OPERATOR VALUE
     */
    private function analyzeExpression(array $expression): void
    {
        $field = strtolower($expression['field']);
        $operator = $expression['operator'];
        $value = $expression['value'];

        /*
         * 1. Check whether the field exists.
         */
        if (!array_key_exists($field, $this->symbolTable)) {
            throw new Exception(
                "Semantic Error: Unknown field '{$expression['field']}'."
            );
        }

        $fieldType = $this->symbolTable[$field];

        /*
         * 2. Check whether the operator is valid
         *    for this field's data type.
         */
        if (
            !in_array(
                $operator,
                $this->operators[$fieldType],
                true
            )
        ) {
            throw new Exception(
                "Semantic Error: Operator '{$operator}' is not valid for field '{$field}'."
            );
        }

        /*
         * 3. Determine the value type.
         */
        $valueType = $this->getValueType($value);

        /*
         * 4. Type checking.
         */
        if ($fieldType === 'NUMBER' && $valueType !== 'NUMBER') {
            throw new Exception(
                "Semantic Error: Field '{$field}' requires a numeric value."
            );
        }

        if ($fieldType === 'STRING' && $valueType !== 'STRING') {
            throw new Exception(
                "Semantic Error: Field '{$field}' requires a string value."
            );
        }
    }

    /**
     * Determine the type of a value.
     */
    private function getValueType(string $value): string
    {
        if (is_numeric($value)) {
            return 'NUMBER';
        }

        return 'STRING';
    }

    /**
     * Return the symbol table.
     */
    public function getSymbolTable(): array
    {
        return $this->symbolTable;
    }
}