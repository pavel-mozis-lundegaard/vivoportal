<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Zend\Mvc\Service\ServiceManagerConfig;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExplorerComponentFactory extends ServiceManager implements
        ServiceLocatorAwareInterface
{

    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->addPeeringServiceManager($serviceLocator);
        $smconfig = new ServiceManagerConfig($this->getServiceConfig());
        $smconfig->configureServiceManager($this);
    }

    protected function getServiceConfig()
    {
        return array(
            'invokables' => array(
            ),
            'factories' => array(
                'editor' => function (ServiceManager $sm) {
                    return new Editor($sm->get('metadata_manager'));
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

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

    }
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

}
