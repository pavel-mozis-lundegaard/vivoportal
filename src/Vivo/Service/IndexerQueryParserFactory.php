<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * IndexerQueryParserFactory
 * Instantiates Indexer Query Parser
 */
class IndexerQueryParserFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $lexer          = new \Vivo\Indexer\Query\Parser\Lexer();
        $rpnConvertor   = new \Vivo\Indexer\Query\Parser\ShuntingYard();
        $queryBuilder   = $serviceLocator->get('indexer_query_builder');
        $parser         = new \Vivo\Indexer\Query\Parser\Parser($lexer, $rpnConvertor, $queryBuilder);
        return $parser;
    }
}