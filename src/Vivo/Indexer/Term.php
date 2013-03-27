<?php
namespace Vivo\Indexer;

/**
 * Term
 * Represents a single query term - a word from text.  This is the unit of search.  It is
 * composed of two elements, the text of the word, as a string, and optionally the name of
 * the field that the text occurred in.
 */
class Term
{
    /**
     * Field name
     * @var string
     */
    protected $field;

    /**
     * Term value
     * @var string
     */
    protected $text;

    /**
     * Constructor
     * @param string $text
     * @param string|null $field
     */
    public function __construct($text, $field = null)
    {
        $this->setText($text);
        $this->setField($field);
    }

    /**
     * Sets the field name
     * @param string|null $field
     */
    public function setField($field = null)
    {
        $this->field = $field;
    }

    /**
     * Returns the field name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Sets the term text
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns the term text
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}