<?php
namespace VivoTest\Indexer\Adapter;

use Vivo\Indexer;

use ZendSearch\Lucene as SearchLucene;

/**
 * LuceneTest
 */
class LuceneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Vivo\Indexer\Adapter\Lucene
     */
    protected $luceneAdapter;

    protected $index;

    public function setUp()
    {
        $this->index            = $this->getMock('ZendSearch\Lucene\SearchIndexInterface', array(), array(), '', false);
        $this->luceneAdapter    = new \Vivo\Indexer\Adapter\Lucene($this->index);
    }

    public function testFind()
    {
        $term       = new Indexer\Term('abcd');
        $query      = new Indexer\Query\Term($term);
        $luceneHits = array(
            new QueryHit
        );
        $this->index->expects($this->once())
            ->method('find')
            ->will($this->returnValue())
        $hits       =  $this->luceneAdapter->find($query);

    }
}