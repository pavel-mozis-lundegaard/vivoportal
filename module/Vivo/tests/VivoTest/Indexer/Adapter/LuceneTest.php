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
        $hit1       = new SearchLucene\Search\QueryHit($this->index);
        $hit1->document_id  = 8;
        $hit1->id           = 8;
        $hit1->score        = 0.5;
        $hit2       = new SearchLucene\Search\QueryHit($this->index);
        $hit2->document_id  = 20;
        $hit2->id           = 20;
        $hit2->score        = 0.3;
        $hit3       = new SearchLucene\Search\QueryHit($this->index);
        $hit3->document_id  = 50;
        $hit3->id           = 50;
        $hit3->score        = 0.1;
        $luceneHits = array(
            $hit1, $hit2, $hit3,
        );
        $this->index->expects($this->exactly(3))
            ->method('isDeleted')
            ->will($this->onConsecutiveCalls(false, true, false));
        $this->index->expects($this->once())
            ->method('find')
            ->will($this->returnValue($luceneHits));
        $hits       =  $this->luceneAdapter->find($query);
        $this->assertEquals(2, count($hits));
        $this->assertEquals(8, $hits[0]->getDocId());
        $this->assertEquals(50, $hits[1]->getDocId());
    }

    public function testTermDocs()
    {
        $term       = new Indexer\Term('abcd');
        $docIds     = array(100, 200, 300);
        $this->index->expects($this->exactly(3))
            ->method('isDeleted')
            ->will($this->onConsecutiveCalls(false, false, true));
        $this->index->expects($this->once())
            ->method('termDocs')
            ->will($this->returnValue($docIds));
        $foundDocIds    = $this->luceneAdapter->termDocs($term);
        $this->assertEquals(array(100, 200), $foundDocIds);
    }

    public function testGetDocumentReturnsNullOnDeleted()
    {
        $docId  = '95';
        $this->index->expects($this->once())
            ->method('isDeleted')
            ->with($docId)
            ->will($this->returnValue(true));
        $this->assertNull($this->luceneAdapter->getDocument($docId));
    }

    public function testGetDocument()
    {
        $docId  = '95';
        $luceneDoc  = new \ZendSearch\Lucene\Document();
        $field      = \ZendSearch\Lucene\Document\Field::keyword('uuid', 'ABCDEF');
        $luceneDoc->addField($field);
        $this->index->expects($this->once())
            ->method('isDeleted')
            ->with($docId)
            ->will($this->returnValue(false));
        $this->index->expects($this->once())
            ->method('getDocument')
            ->with($docId)
            ->will($this->returnValue($luceneDoc));
        $doc    = $this->luceneAdapter->getDocument($docId);
        $this->assertEquals('ABCDEF', $doc->getFieldValue('uuid'));
    }
}