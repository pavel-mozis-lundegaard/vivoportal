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
        $cmsApi             = $serviceLocator->get('Vivo\CMS\Api\CMS');
        $symRefConvertor    = $serviceLocator->get('sym_ref_convertor');

        $adapter            = new WysiwygAdapter($cmsApi, $symRefConvertor);
        return $adapter;
    }

}
