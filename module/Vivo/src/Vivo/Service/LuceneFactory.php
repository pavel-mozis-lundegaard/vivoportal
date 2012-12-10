<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * LuceneFactory
 */
class LuceneFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws \ZendSearch\Lucene\Exception\RuntimeException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storageConfig  = array(
            'class'     => 'Vivo\Storage\LocalFileSystemStorage',
            'options'   => array(
                'root'          => __DIR__ . '/../../../../../data/lucene',
                'path_builder'  => $serviceLocator->get('path_builder'),
            ),
        );
        $storageFactory = $serviceLocator->get('storage_factory');
        /* @var $storageFactory \Vivo\Storage\Factory */
        $storage    = $storageFactory->create($storageConfig);
        $luceneDirPath  = '/';
        $luceneDir  = new \Vivo\ZendSearch\Lucene\Storage\Directory\VivoStorage($storage, $luceneDirPath);
        try {
            $index      = \ZendSearch\Lucene\Lucene::open($luceneDir);
        } catch (\ZendSearch\Lucene\Exception\RuntimeException $e) {
            if ($e->getMessage() == 'Index doesn\'t exists in the specified directory.') {
                //Index not created yet, create it
                $index      = \ZendSearch\Lucene\Lucene::create($luceneDir);
            } else {
                throw $e;
            }
        }
        return $index;
    }
}
