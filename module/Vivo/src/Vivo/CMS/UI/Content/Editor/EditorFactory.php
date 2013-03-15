<?php
namespace Vivo\CMS\UI\Content\Editor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $docApi = $serviceLocator->get('Vivo\CMS\Api\Document');

        $editor = new Editor($docApi);

        return $editor;
    }

}
