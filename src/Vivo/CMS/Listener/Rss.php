<?php
namespace Vivo\CMS\Listener;

use Vivo\CMS\Event\CMSEvent;
use Vivo\CMS\UI;
use Vivo\SiteManager\Event\SiteEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\PhpEnvironment\Request;

class Rss implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    /**
     * @param \Zend\Http\PhpEnvironment\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(CMSEvent::EVENT_CREATE, array($this, 'invoke'), $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function invoke(CMSEvent $e)
    {
        if($this->request->getUri()->getQuery() == 'rss') {
            $rss = new UI\Rss();
            $rss->setCmsEvent($e);
            $rss->setDocument($e->getDocument());

            return $rss;
        }

        return null;
    }

}
