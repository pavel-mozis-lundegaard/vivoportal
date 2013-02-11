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
     * Set to false, because we don't want share services from peering SM.
     * @var boolean
     */
    protected $shareByDefault = false;

    /**
     * Constructor.
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceManager $sm)
    {
         $this->addPeeringServiceManager($sm);
         //we use same initializers as main service manager
         $this->initializers = $sm->initializers;
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
                    $editor = new Editor($sm->get('Vivo\CMS\Api\CMS'), $sm->get('metadata_manager'));
                    $editor->setRequest($sm->get('request'));
                    $editor->setTabContainer($sm->get('Vivo\UI\TabContainer'), 'contentTab');

                    return $editor;
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
