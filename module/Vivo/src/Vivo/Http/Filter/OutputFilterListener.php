<?php
namespace Vivo\Http\Filter;

use Vivo\Http\Filter\Exception\CanNotAttachFilterException;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * OutputFilterListener is responsible for attaching all registered output filters.
 *
 */

class OutputFilterListener implements ListenerAggregateInterface,
        ServiceLocatorAwareInterface, EventManagerAwareInterface
{

    /**
     * @var CallbackHandler
     */
    protected $listener;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listener = $events
                ->attach(MvcEvent::EVENT_FINISH,
                        array($this, 'attachOutputFilters'), -1000);
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\ListenerAggregateInterface::detach()
     */
    public function detach(EventManagerInterface $events)
    {
        $events->detach($this->listener);
    }

    /**
     * Callback for attaching response output filters.
     * @param MvcEvent $e
     * @throws CanNotAttachFilterException
     */
    public function attachOutputFilters(MvcEvent $e)
    {
        //TODO: move config key outside this class
        $cmsConfig  = $this->serviceLocator->get('cms_config');
        if (isset($cmsConfig['output_filters']) &&
                is_array($filters = $cmsConfig['output_filters']))
        {
            foreach ($filters as $outputFilter) {
                try {
                    $filter = $this->serviceLocator->get($outputFilter);
                } catch (\Exception $e) {
                    throw new CanNotAttachFilterException(
                            sprintf(
                                    "%s: Can not create filter instance for `%s`.",
                                    __METHOD__, $outputFilter), 500, $e);
                }
                if (!$filter instanceof OutputFilterInterface) {
                    throw new CanNotAttachFilterException(
                            sprintf(
                                    "%s: Filter `%s` is not instance of OutputFilterInterface.",
                                    __METHOD__, $outputFilter));
                }
                $attached = $filter->attachFilter($e->getRequest(), $e->getResponse());
                $message = "Output filter '$outputFilter' ".($attached ?  '' : 'not '). "attached.";
                $this->getEventManager()->trigger('log', $this, array ('message' => $message));
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
        $this->events->addIdentifiers(__CLASS__);
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->events;
    }
}

