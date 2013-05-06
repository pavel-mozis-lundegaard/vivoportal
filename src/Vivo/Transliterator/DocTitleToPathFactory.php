<?php
namespace Vivo\Transliterator;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * DocTitleToPathFactory
 */
class DocTitleToPathFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config     = $serviceLocator->get('config');
        if (isset($config['transliterator']['doc_title_to_path']['options'])) {
            $options    = $config['transliterator']['doc_title_to_path']['options'];
        } else {
            $options    = array();
        }
        $translit   = new Transliterator($options);
        return $translit;
    }
}
