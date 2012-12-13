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

    public function xtestFind()
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

    public function xtestTermDocs()
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

    public function xtestGetDocumentReturnsNullOnDeleted()
    {
        $docId  = '95';
        $this->index->expects($this->once())
            ->method('isDeleted')
            ->with($docId)
            ->will($this->returnValue(true));
        $this->assertNull($this->luceneAdapter->getDocument($docId));
    }

    public function xtestGetDocument()
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

    public function xtestGetDocumentReturnsNullOnNonExistent()
    {
        $docId  = '95';
        $this->index->expects($this->once())
            ->method('isDeleted')
            ->with($docId)
            ->will($this->returnValue(false));
        $this->index->expects($this->once())
            ->method('getDocument')
            ->with($docId)
            ->will($this->throwException(new SearchLucene\Exception\OutOfRangeException()));
        $this->assertNull($this->luceneAdapter->getDocument($docId));
    }

    public function xtestAddDocumentCommitsOutsideTx()
    {
        $this->assertFalse($this->luceneAdapter->isTransactionOpen());
        $this->index->expects($this->once())
            ->method('addDocument');
        $doc    = new \Vivo\Indexer\Document();
        $this->luceneAdapter->addDocument($doc);
    }

    public function xtestAddDocumentDoesNotCommitWithinTx()
    {
        $this->luceneAdapter->begin();
        $this->assertTrue($this->luceneAdapter->isTransactionOpen());
        $this->index->expects($this->never())
            ->method('addDocument');
        $doc    = new \Vivo\Indexer\Document();
        $this->luceneAdapter->addDocument($doc);
    }

    public function xtestDeleteDocumentCommitsOutsideTx()
    {
        $this->assertFalse($this->luceneAdapter->isTransactionOpen());
        $docId  = '501';
        $this->index->expects($this->once())
            ->method('delete')
            ->with($docId);
        $this->luceneAdapter->deleteDocument($docId);
    }

    public function xtestDeleteDocumentDoesNotCommitWithinTx()
    {
        $this->luceneAdapter->begin();
        $this->assertTrue($this->luceneAdapter->isTransactionOpen());
        $docId  = '501';
        $this->index->expects($this->never())
            ->method('delete');
        $this->luceneAdapter->deleteDocument($docId);
    }

    public function xtestCommit()
    {
        //Previous transaction
        $this->luceneAdapter->deleteDocument('333');
        $doc    = new \Vivo\Indexer\Document();
        $this->luceneAdapter->addDocument($doc);
        $this->luceneAdapter->begin();
        //Deletions
        $deleteIds  = array('8', '950', '402', '19');
        foreach ($deleteIds as $deleteId) {
            $this->luceneAdapter->deleteDocument($deleteId);
        }
        $this->index->expects($this->exactly(count($deleteIds)))
            ->method('delete');
        $this->index->expects($this->at(0))
            ->method('delete')
            ->with('8');
        $this->index->expects($this->at(1))
            ->method('delete')
            ->with('950');
        $this->index->expects($this->at(2))
            ->method('delete')
            ->with('402')
            ->will($this->throwException(new SearchLucene\Exception\OutOfRangeException()));
        $this->index->expects($this->at(3))
            ->method('delete')
            ->with('19');
        $this->index->expects($this->once())
            ->method('commit');
        //Added docs
        $doc1   = new \Vivo\Indexer\Document();
        $doc2   = new \Vivo\Indexer\Document();
        $this->luceneAdapter->addDocument($doc1);
        $this->luceneAdapter->addDocument($doc2);
        $this->index->expects($this->exactly(2))
            ->method('addDocument');
        //Commit
        $this->luceneAdapter->commit();
        $this->assertFalse($this->luceneAdapter->isTransactionOpen());
    }

    public function xtestBegin()
    {
        $this->assertFalse($this->luceneAdapter->isTransactionOpen());
        $this->luceneAdapter->begin();
        $this->assertTrue($this->luceneAdapter->isTransactionOpen());
    }

    public function xtestRollback()
    {
        $this->luceneAdapter->begin();
        $this->luceneAdapter->deleteDocument('333');
        $doc    = new \Vivo\Indexer\Document();
        $this->luceneAdapter->addDocument($doc);
        $this->luceneAdapter->rollback();
        $this->assertFalse($this->luceneAdapter->isTransactionOpen());
    }

    public function xtestDeleteAllDocuments()
    {
        $docCount   = 5;
        $this->index->expects($this->exactly(2))
            ->method('commit');
        $this->index->expects($this->once())
            ->method('maxDoc')
            ->will($this->returnValue($docCount));
        $this->index->expects($this->exactly($docCount))
            ->method('delete');
        //Delete is first called at the index 2 (the 'at' matcher matches 'per object'!)
        $this->index->expects($this->at(2))
            ->method('delete')
            ->with(0)
            ->will($this->throwException(new SearchLucene\Exception\OutOfRangeException()));
        for ($i = 1; $i < $docCount; $i++) {
            $this->index->expects($this->at(2 + $i))
                ->method('delete')
                ->with($i);
        }
        $this->luceneAdapter->deleteAllDocuments();
    }
}