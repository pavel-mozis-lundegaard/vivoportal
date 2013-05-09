<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerAdapterFactory
 * Instantiates an Indexer Adapter
 */
class IndexerAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\UnsupportedIndexerAdapterException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('config');
        $idxAdapterCfg  = $config['indexer']['adapter'];
        $type           = $idxAdapterCfg['type'];
        switch ($type) {
            case 'dummy':
                $adapter    = new \Vivo\Indexer\Adapter\Dummy();
                break;
            case 'solr':
                $options        = $idxAdapterCfg['options'];
                $idField        = $options['id_field'];
                $fieldHelper    = $serviceLocator->get('indexer_field_helper');
                $solrService    = $serviceLocator->get('VpApacheSolr\solr_service');
                $adapter        =  new \Vivo\Indexer\Adapter\Solr($solrService, $idField, $fieldHelper);
                break;
            default:
                throw new Exception\UnsupportedIndexerAdapterException(
                    sprintf("%s: Unsupported indexer adapter type '%s'", __METHOD__, $type));
                break;
        }
        return $adapter;
    }
}
