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
        $this->markTestIncomplete();
    }
}