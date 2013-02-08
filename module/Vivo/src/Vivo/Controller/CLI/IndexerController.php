<?php
namespace Vivo\Controller\CLI;

use Vivo\Indexer\IndexerInterface;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;
use Vivo\SiteManager\Event\SiteEventInterface;

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
     * Indexer API
     * @var IndexerApiInterface
     */
    protected $indexerApi;

    /**
     * Site Event
     * @var SiteEventInterface
     */
    protected $siteEvent;

    /**
     * Constructor
     * @param \Vivo\Indexer\IndexerInterface $indexer
     * @param \Vivo\CMS\Api\IndexerInterface $indexerApi
     * @param \Vivo\SiteManager\Event\SiteEventInterface $siteEvent
     */
    public function __construct(IndexerInterface $indexer, IndexerApiInterface $indexerApi,
                                SiteEventInterface $siteEvent)
    {
        $this->indexer      = $indexer;
        $this->indexerApi   = $indexerApi;
        $this->siteEvent    = $siteEvent;
    }

    public function getConsoleUsage()
    {
        $output = "\nIndexer usage:";
        $output .= "\n\nindexer clear";
        $output .= "\nindexer reindex <host>";
        return $output;
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

    /**
     * Reindex action
     * @return string
     */
    public function reindexAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $host       = $request->getParam('host');
        $site       = $this->siteEvent->getSite();
        if (!$site) {
            $output = sprintf("No site object created; host = '%s'", $host);
            return $output;
        }
        $path       = $site->getPath();
        $numIndexed = $this->indexerApi->reindex($path, true);
        $output     = sprintf("%s items reindexed", $numIndexed);
        return $output;
    }
}
