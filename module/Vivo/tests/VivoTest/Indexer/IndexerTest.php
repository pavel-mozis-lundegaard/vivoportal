<?php
namespace VivoTest\Indexer;

use Vivo\Indexer\Indexer;
use Vivo\Indexer\QueryHit;
use Vivo\Indexer\Adapter\AdapterInterface;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * Indexer adapter
     * @var AdapterInterface
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter  = $this->getMock('Vivo\Indexer\Adapter\AdapterInterface', array(), array(), '', false);
        $this->indexer  = new Indexer($this->adapter);
    }

    public function testDelete()
    {
        $query  = $this->getMock('Vivo\Indexer\Query\QueryInterface', array(), array(), '', false);
        $hit1   = new QueryHit($this->adapter, '9', '9', 0.7);
        $hit2   = new QueryHit($this->adapter, '53', '53', 0.2);
        $hits   = array(
            $hit1, $hit2,
        );
        $this->adapter->expects($this->once())
            ->method('find')
            ->with($query)
            ->will($this->returnValue($hits));
        $this->adapter->expects($this->exactly(count($hits)))
            ->method('deleteDocument');
        $this->adapter->expects($this->at(1))
            ->method('deleteDocument')
            ->with('9');
        $this->adapter->expects($this->at(2))
            ->method('deleteDocument')
            ->with('53');
        $this->indexer->delete($query);
        $this->indexer->commit();
    }
}