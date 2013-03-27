<?php
namespace Vivo\Log;

use Zend\Log\LoggerInterface;

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedEventManagerAwareInterface;

/**
 * Event listener for logging. The listener is attached to shared event manager and log event.
 * @todo avoid loging same event more than once.
 */

class EventListener implements SharedEventManagerAwareInterface
{
    /**
     * Logger.
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Shared event manager.
     * @var SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * Options
     * @example
     *  array (
            'attach' => array (
                array('*', 'log') //log 'log' events
                array('*', '*') //log all events
            )
        )
     *
     * @var array
     */
    protected $options = array (
            'attach' => array (
                array('*', 'log'), //log 'log' events
//                array('*', '*'), //log all events
            )
        );

   /**
    * Constructor.
    * @param LoggerInterface $logger
    * @param array $options
    */
   public function __construct(LoggerInterface $logger, array $options = array())
    {
        $this->logger = $logger;
        if ($options) {
            $this->options = $options;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\SharedEventManagerAwareInterface::setSharedManager()
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedEventManager = $sharedEventManager;
        //attach listener to all events
        foreach ($this->options['attach'] as $rule) {
            $this->sharedEventManager->attach($rule[0], $rule[1], array($this, 'log'));
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\SharedEventManagerAwareInterface::getSharedManager()
     */
    public function getSharedManager()
    {
        return $this->sharedEventManager;
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\SharedEventManagerAwareInterface::unsetSharedManager()
     */
    public function unsetSharedManager()
    {
        $this->sharedEventManager = null;
    }

    /**
     * Log callback function attached to shared event manager.
     * @param Event $event
     */
    public function log(Event $event)
    {
        if ($event->getName() == 'log') {
            // when event 'log' is triggered - this event hasn't any function except log something.
            $log = $event->getParams();
        } else {
            //when other events are triggered
            $log = $event->getParam('log', array ());
        }

        $source = is_object($event->getTarget()) ? get_class($event->getTarget()) : $event->getTarget();
        $priority = isset($log['priority']) ? $log['priority'] : Logger::INFO;
        //FIXME: setting priority to Logger::DEBUG causes problems with FirePHP writer

        $message  = $source . "#" . $event->getName() .
                (isset($log['message']) ? ': ' . $log['message'] : '');

        //extra infromation for custom writers
        $extra = isset($log['extra']) && ise_array($log['extra']) ? $log['extra'] : array();
        $extra['source'] = $source;
        $extra['event'] = $event->getName();
        $extra['message'] = isset($log['message']) ? $log['message'] : null;

        $this->logger->log($priority, $message, $extra);

        //return true to continue with event propagation.
        return true;
    }
}
