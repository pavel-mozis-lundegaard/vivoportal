<?php
namespace Vivo\Backend\UI\Explorer;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Editor factory.
 */
class FinderFactory implements FactoryInterface
{
    /**
     * Create Finder
     * @param ServiceLocatorInterface $serviceLocator
     * @return Finder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $finder = new Finder();
        $finder->setAlert($serviceLocator->get('Vivo\UI\Alert'));
        return $finder;
    }
}
