<?php
namespace Vivo\Transliterator;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * UrlFactory
 */
class UrlFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config     = $serviceLocator->get('config');
        if (isset($config['transliterator']['url']['options'])) {
            $options    = $config['transliterator']['url']['options'];
        } else {
            $options    = array();
        }
        $translit   = new Url($options);
        return $translit;
    }
}
