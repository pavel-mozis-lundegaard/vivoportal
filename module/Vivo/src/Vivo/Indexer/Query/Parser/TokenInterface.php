<?php
namespace Vivo\Indexer\Query\Parser;

/**
 * TokenInterface
 */
interface TokenInterface
{
    /**
     * Token types
     */
    const TYPE_LEFT_PARENTHESIS     = 'left_parenthesis';
    const TYPE_RIGHT_PARENTHESIS    = 'right_parenthesis';
    const TYPE_FIELD_LIKE           = 'field_like';
    const TYPE_FIELD_NAME           = 'field_name';
    const TYPE_STRING_LITERAL       = 'string_literal';
    const TYPE_RANGE                = 'range';
    const TYPE_OPERATOR             = 'operator';

    /**
     * Returns the token type
     * @return string
     */
    public function getType();

    /**
     * Sets the token type
     * @param string $type
     */
    public function setType($type = null);

    /**
     * Returns the token lexeme
     * @return string
     */
    public function getLexeme();

    /**
     * Sets the token lexeme
     * @param string $lexeme
     */
    public function setLexeme($lexeme = null);

    /**
     * Returns the token value
     * @return mixed
     */
    public function getValue();

    /**
     * Sets the token value
     * @param mixed $value
     */
    public function setValue($value = null);

    /**
     * Returns the lexeme position in the input text
     * @return integer
     */
    public function getPosition();

    /**
     * Sets the lexeme position in the input text
     * @param int $position
     */
    public function setPosition($position = null);
}
