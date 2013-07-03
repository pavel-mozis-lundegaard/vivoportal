<?php
namespace Vivo\CMS;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Default listener for fetching error documents.
 *
 * This listener uses a config to map an errorcode to an error document path.
 */
class FetchErrorDocumentListener implements ListenerAggregateInterface
{

    /**
     * Indexer api
     * @var Api\Indexer
     */
    protected $indexerApi;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Configuration.
     * @example array ('codes' => array('404' => '/error-404/'), 'default'=> '/error/')
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param array $config
     */
    public function __construct(Api\CMS $cmsApi, array $config)
    {

        $this->config = $config;
        $this->cmsApi = $cmsApi;
    }

    /**
     * Attach to an event manager.
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Event\CMSEvent::EVENT_FETCH_ERRORDOCUMENT, array($this, 'fetchDocument'));
    }

    /**
     * Detach all our listeners from the event manager.
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Fetch error document.
     * @param \Vivo\CMS\Event\CMSEvent $cmsEvent
     * @return null|Model\Document
     */
    public function fetchDocument(Event\CMSEvent $cmsEvent)
    {
        $exceptionCode = $cmsEvent->getException()->getCode();
        foreach($this->config['code'] as $code => $path){
            if ($exceptionCode == $code) {
                $docPath = $path;
                break;
            }
        }

        if (!isset($docPath) && isset($this->config['default'])) {
            $docPath = $this->config['default'];
        }

        if (!$docPath) {
            return null;
        }

        try {
            $document = $this->cmsApi->getSiteEntity($docPath, $cmsEvent->getSite());
        } catch (\Vivo\Repository\Exception\EntityNotFoundException $e) {
            return null;
        }
        return $document;
    }
}
