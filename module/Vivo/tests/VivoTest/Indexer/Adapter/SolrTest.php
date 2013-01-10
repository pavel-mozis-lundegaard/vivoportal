<?php
namespace VivoTest\Indexer\Adapter;

use Vivo\Indexer;
use Vivo\Indexer\Adapter\Solr as SolrAdapter;
use Vivo\Indexer\Query\Term as TermQuery;
use Vivo\Indexer\Query\BooleanNot;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\QueryParams;

use ApacheSolr\Service as SolrService;
use ApacheSolr\Response as SolrResponse;
use ApacheSolr\HttpTransport\Response as SolrHttpResponse;

class SolrResponseMock
{
    public $numFound    = 0;
    public $docs        = array();
}

class SolrResultMock
{
    public $response;

    public function __construct(SolrResponseMock $solrResponse)
    {
        $this->response = $solrResponse;
    }
}

/**
 * SolrTest
 */
class SolrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SolrAdapter;
     */
    protected $adapter;

    /**
     * @var SolrService
     */
    protected $solrService;

    public function setUp()
    {
        $this->solrService  = $this->getMock('ApacheSolr\Service', array('search'), array(), '', false);
        $this->adapter      = new SolrAdapter($this->solrService, 'uuid');
    }

    public function testBuildNotQuery()
    {
        $queryParams    = new QueryParams();
        $queryParams->setPageSize(10);
        $queryParams->setStartOffset(0);
        $query  = new TermQuery(new IndexerTerm('bar', 'fieldBar'));
        $query  = new BooleanNot($query);
        $solrQuery  = '(NOT fieldBar:bar)';
        $solrResponse   = new SolrResponseMock();
        $solrResult = new SolrResultMock($solrResponse);
        $this->solrService->expects($this->once())
            ->method('search')
            ->with($this->equalTo($solrQuery))
            ->will($this->returnValue($solrResult));
        $result = $this->adapter->find($query, $queryParams);
    }

}