<?php
namespace Vivo\Indexer\Query\Parser;

/**
 * RpnConvertorInterface
 */
interface RpnConvertorInterface
{
    /**
     * Converts the stream of tokens into RPN (ReversePolishNotation)
     * @param TokenInterface[] $tokens
     * @return TokenInterface[]
     */
    public function getRpn(array $tokens);
}