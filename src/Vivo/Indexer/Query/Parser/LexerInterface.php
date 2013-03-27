<?php
namespace Vivo\Indexer\Query\Parser;

/**
 * LexerInterface
 */
interface LexerInterface
{
    /**
     * Tokenizes input text (UTF-8 encoded)
     * @param $text
     * @return TokenInterface[]
     */
    public function tokenize($text);
}