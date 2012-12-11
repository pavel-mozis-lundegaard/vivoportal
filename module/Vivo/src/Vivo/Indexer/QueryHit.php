<?php
namespace Vivo\Indexer;

/**
 * QueryHit
 */
class QueryHit
{
    /**
     * Unique hit id
     * @var string
     */
    protected $id;

    /**
     * Score of the hit in the
     * @var float
     */
    protected $score;

    /**
     * Index document
     * @var DocumentInterface
     */
    protected $document;

    /**
     * Constructor
     * @param mixed $id
     * @param mixed $score
     * @param DocumentInterface $document
     */
    public function __construct($id, $score, DocumentInterface $document)
    {
        $this->id       = $id;
        $this->score    = $score;
        $this->document = $document;
    }

    /**
     * Returns the unique hit id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the hit score
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Returns document
     * @return \Vivo\Indexer\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}
