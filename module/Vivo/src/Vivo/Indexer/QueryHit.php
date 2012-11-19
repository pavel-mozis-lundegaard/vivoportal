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
     * Indexer adapter used to lazy load the document
     * @var Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor
     * @param Adapter\AdapterInterface $adapter
     * @param string $id
     * @param string $docId
     * @param float $score
     */
    public function __construct(Adapter\AdapterInterface $adapter, $id, $docId, $score)
    {
        $this->adapter  = $adapter;
        $this->id       = $id;
        $this->docId    = $docId;
        $this->score    = $score;
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
        if (!$this->document) {
            $this->document = $this->adapter->getDocument($this->docId);
        }
        return $this->document;
    }
}