<?php
namespace Vivo\Controller\CLI;

use Vivo\Indexer\IndexerInterface;

/**
 * Vivo CLI controller for command 'indexer'
 */
class IndexerController extends AbstractCliController
{
    const COMMAND = 'indexer';

    /**
     * Indexer
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * Constructor
     * @param \Vivo\Indexer\IndexerInterface $indexer
     */
    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer  = $indexer;
    }

    public function getConsoleUsage()
    {
        return 'indexer usage: ...';
    }

    /**
     * Clears all documents from index
     * @return string
     */
    public function clearAction()
    {
        $this->indexer->deleteAllDocuments();
        $output = "Attempted deletion of all documents from index";
        return $output;
    }
}
