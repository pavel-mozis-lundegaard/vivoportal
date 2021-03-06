<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NavigationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $docApi = $serviceLocator->get('Vivo\CMS\Api\Document');
        $config = $serviceLocator->get('cms_config');
        
        $editor = new Navigation($docApi, $config['document_sorting']['native']);
        return $editor;
    }

}
