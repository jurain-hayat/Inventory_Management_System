<?php

class CompilerError extends Exception
{
    private string $stage;

    public function __construct(string $stage, string $message)
    {
        $this->stage = $stage;

        parent::__construct($message);
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function getFormattedMessage(): string
    {
        return $this->stage . " Error: " . $this->getMessage();
    }
}


/*
 * Create a lexical error
 */
function lexicalError(string $message): CompilerError
{
    return new CompilerError(
        "Lexical",
        $message
    );
}


/*
 * Create a syntax error
 */
function syntaxError(string $message): CompilerError
{
    return new CompilerError(
        "Syntax",
        $message
    );
}


/*
 * Create a semantic error
 */
function semanticError(string $message): CompilerError
{
    return new CompilerError(
        "Semantic",
        $message
    );
}