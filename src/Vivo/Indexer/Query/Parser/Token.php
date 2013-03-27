<?php
namespace Vivo\Indexer\Query\Parser;

/**
 * Token
 */
class Token implements TokenInterface
{
    /**
     * Token type
     * @var string
     */
    protected $type;

    /**
     * Token lexeme
     * @var string
     */
    protected $lexeme;

    /**
     * Token value
     * @var mixed
     */
    protected $value;

    /**
     * Lexeme position in the input text
     * @var integer
     */
    protected $position;

    /**
     * Construct
     * @param string $type
     * @param string $lexeme
     * @param integer $position
     * @param string $value
     */
    public function __construct($type = null, $lexeme = null, $position = null, $value = null)
    {
        $this->setType($type);
        $this->setLexeme($lexeme);
        $this->setPosition($position);
        $this->setValue($value);
    }

    /**
     * Returns the token type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the token type
     * @param string $type
     */
    public function setType($type = null)
    {
        $this->type = $type;
    }

    /**
     * Returns the token lexeme
     * @return string
     */
    public function getLexeme()
    {
        return $this->lexeme;
    }

    /**
     * Sets the token lexeme
     * @param string $lexeme
     */
    public function setLexeme($lexeme = null)
    {
        $this->lexeme = $lexeme;
    }

    /**
     * Returns the token value
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the token value
     * @param mixed $value
     */
    public function setValue($value = null)
    {
        $this->value = $value;
    }

    /**
     * Returns the lexeme position in the input text
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the lexeme position in the input text
     * @param int $position
     */
    public function setPosition($position = null)
    {
        $this->position = $position;
    }
}