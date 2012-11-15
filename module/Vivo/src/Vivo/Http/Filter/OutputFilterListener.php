<?php
namespace Vivo\Http\Filter;

use Zend\Mvc\MvcEvent;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class OutputFilterListener implements ListenerAggregateInterface
{
    protected $listener;

    public function attach(EventManagerInterface $events)
    {
        $this->listener = $events
                ->attach(MvcEvent::EVENT_FINISH, array($this, 'doFilters'),
                        -1000);
    }

    public function detach(EventManagerInterface $events)
    {

    }

    public function doFilters(MvcEvent $e) {
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('config');

        foreach ($config['vivo']['output_filters'] as $filterClass) {
            $filter = new $filterClass();
            $filter->doFilter($e->getRequest(), $e->getResponse());
        }
    }

}
