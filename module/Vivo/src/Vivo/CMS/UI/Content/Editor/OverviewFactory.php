<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OverviewFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $editor = new Overview();

        return $editor;
    }

}
