<?php
namespace VivoTest\Indexer;

use Vivo\Indexer\Result;
use Vivo\Indexer\QueryHit;
use Vivo\Indexer\Document;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testCurrent()
    {
        $hit1   = new QueryHit('id1', 0.4, new Document('id1', array('field1' => 'foo')));
        $hit2   = new QueryHit('id2', 0.3, new Document('id2', array('field2' => 'bar')));
        $hits   = array($hit1, $hit2);
        $result = new Result($hits, count($hits));
        $curr   = current($hits);
        $this->assertSame($hit1, $curr);
    }
}