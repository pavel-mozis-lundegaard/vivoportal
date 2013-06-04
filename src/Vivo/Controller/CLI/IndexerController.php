<?php
namespace Vivo\Controller\CLI;

use Vivo\Indexer\IndexerInterface;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;
use Vivo\SiteManager\Event\SiteEventInterface;
use Vivo\Indexer\IndexerEvent;

use Zend\EventManager\EventManagerInterface;

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
     * Indexer event manager
     * @var EventManagerInterface
     */
    protected $indexerEvents;

    /**
     * Array of failed paths during reindexing
     * @var array
     */
    protected $failed   = array();

    /**
     * Constructor
     * @param \Vivo\Indexer\IndexerInterface $indexer
     * @param \Vivo\CMS\Api\IndexerInterface $indexerApi
     * @param \Vivo\SiteManager\Event\SiteEventInterface $siteEvent
     * @param \Zend\EventManager\EventManagerInterface $indexerEvents
     */
    public function __construct(IndexerInterface $indexer,
                                IndexerApiInterface $indexerApi,
                                SiteEventInterface $siteEvent,
                                EventManagerInterface $indexerEvents)
    {
        $this->indexer          = $indexer;
        $this->indexerApi       = $indexerApi;
        $this->siteEvent        = $siteEvent;
        $this->indexerEvents    = $indexerEvents;
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
        $this->failed   = array();
        //Attach listeners
        $failedListener = $this->indexerEvents->attach(IndexerEvent::EVENT_INDEX_FAILED, array($this, 'onReindexFail'));
        $postListener   = $this->indexerEvents->attach(IndexerEvent::EVENT_INDEX_POST, array($this, 'onReindexPost'));
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $host       = $request->getParam('host');
        echo PHP_EOL . 'Reindex' . PHP_EOL;
        echo 'Host: ' . $host . PHP_EOL;
        $site       = $this->siteEvent->getSite();
        if (!$site) {
            $output = sprintf("No site object created");
            return $output;
        }
        echo 'Site path: '. $site->getPath() . PHP_EOL;
        $numIndexed = $this->indexerApi->reindex($site, '/', true, true);
        //Detach listeners
        $output     = sprintf("%s items reindexed", $numIndexed) . PHP_EOL;
        if (count($this->failed)) {
            $output     .= PHP_EOL . 'Failed paths:' . PHP_EOL;
            $output     .= implode(PHP_EOL, $this->failed);
        }
        $output     .= PHP_EOL;
        $this->indexerEvents->detach($failedListener);
        $this->indexerEvents->detach($postListener);
        return $output;
    }

    /**
     * Listener for failed indexing attempts
     * @param IndexerEvent $e
     */
    public function onReindexFail(IndexerEvent $e)
    {
        echo PHP_EOL . 'Error: ' . $e->getEntityPath() . PHP_EOL . PHP_EOL;
        $this->failed[] = $e->getEntityPath();
    }

    /**
     * Listener for index_post events
     * @param IndexerEvent $e
     */
    public function onReindexPost(IndexerEvent $e)
    {
        echo 'Reindexed: ' . $e->getEntityPath() . PHP_EOL;
    }
}
