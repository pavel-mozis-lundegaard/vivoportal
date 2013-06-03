<?php
namespace Vivo\CMS\Api;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for Site api service.
 *
 */
class SiteFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cms            = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $repository     = $serviceLocator->get('repository');
        $indexerApi     = $serviceLocator->get('Vivo\CMS\Api\Indexer');
        $queryBuilder   = $serviceLocator->get('indexer_query_builder');
        $pathBuilder    = $serviceLocator->get('path_builder');
        $siteApi        = new Site($cms, $repository, $indexerApi, $queryBuilder, $pathBuilder);
        //PerfLog
        $events         = $serviceLocator->get('event_manager');
        $events->trigger('log', $this,
            array ('message'    => 'Site Api created',
                'priority'   => \VpLogger\Log\Logger::PERF_FINER));

        return $siteApi;
    }
}
