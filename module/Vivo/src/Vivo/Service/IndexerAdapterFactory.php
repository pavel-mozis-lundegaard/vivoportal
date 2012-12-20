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
        $idxConfig      = $config['vivo']['indexer'];
        $idxAdapterCfg  = $idxConfig['adapter'];
        $type           = $idxAdapterCfg['type'];
        $options        = $idxAdapterCfg['options'];
        $fieldHelper    = $serviceLocator->get('indexer_field_helper');
        switch ($type) {
            case 'dummy':
                $adapter    = new \Vivo\Indexer\Adapter\Dummy();
                break;
            case 'solr':
                $idField        = $options['id_field'];
                $solrSvcOpts    = $options['solr_service'];
                $solrService    = new \ApacheSolr\Service($solrSvcOpts['host'],
                                                          $solrSvcOpts['port'],
                                                          $solrSvcOpts['path']);
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
