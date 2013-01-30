<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating ui component used in Explorer (editor, viewer, browser etc.)
 */
class ExplorerComponentFactory extends ServiceManager
{

    /**
     * Constructor.
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
         $this->setService('service_locator', $serviceLocator);
         $config = $this->getServiceConfig();
         foreach ($config['factories'] as $name => $factory)
         {
             $this->setFactory($name, $factory);
         }
    }

    /**
     * Returns service manager configuration for explorer components
     * @return array
     */
    protected function getServiceConfig()
    {
        return array(
            'invokables' => array(
            ),
            'factories' => array(
                'editor' => function (ServiceManager $sm) {
                    return new Editor($sm->get('service_locator')->get('metadata_manager'));
                },
                'viewer' => function (ServiceManager $sm) {
                    $viewer = new Viewer();
                    return $viewer;
                },
                'browser' => function (ServiceManager $sm) {
                    return new Browser();
                },
            ),
            'aliases' => array(
            ),
        );
    }
}
