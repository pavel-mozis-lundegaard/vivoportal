<?php
namespace Vivo\Service;

use Vivo\Log\EventListener;

use Zend\Log\Logger;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Logger factory.
 */
class LoggerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logger = new \Vivo\Log\Logger();

        if (defined('REQUEST_START')) {
            $logger->setStart(REQUEST_START);
        }

        //add main service manager as peering sm
        $logger->getWriterPluginManager()->addPeeringServiceManager($serviceLocator);

        $config = $serviceLocator->get('config');
        $config = $config['logger'];

        if (!count($config['writers'])) {
            $logger->addWriter('null');
        } else {
            foreach ($config['writers'] as $writer) {
                    $logger->addWriter($writer);
            }
            $logger->log(Logger::INFO, 'Logger init.'.$lo);
            $eventListener = new EventListener($logger, $config['listener']);
            $eventListener->setSharedManager($serviceLocator->get('shared_event_manager'));
        }

        return $logger;
    }
}
