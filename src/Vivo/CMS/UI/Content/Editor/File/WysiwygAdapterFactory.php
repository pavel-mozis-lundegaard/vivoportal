<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates Wysiwyg Adapter instance
 *
 */
class WysiwygAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fileApi            = $serviceLocator->get('Vivo\CMS\Api\Content\File');
        $symRefConvertor    = $serviceLocator->get('sym_ref_convertor');
        $formFactory        = $serviceLocator->get('form_factory');

        $adapter            = new WysiwygAdapter($fileApi, $symRefConvertor, $formFactory);
        return $adapter;
    }

}
