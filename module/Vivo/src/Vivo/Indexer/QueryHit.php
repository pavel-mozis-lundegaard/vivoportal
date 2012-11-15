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
     * Document id within index
     * @var string
     */
    protected $docId;

    /**
     * Score of the hit in the
     * @var float
     */
    protected $score;

    /**
     * Document found in the index
     * @var Document
     */
    protected $document;

    /**
     * Constructor
     * @param string $id
     * @param string $docId
     * @param float $score
     * @param Document $document
     */
    public function __construct($id, $docId, $score, Document $document)
    {
        $this->id       = $id;
        $this->docId    = $docId;
        $this->score    = $score;
        $this->document = $document;
    }

    /**
     * Returns the document id
     * @return string
     */
    public function getDocId()
    {
        return $this->docId;
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