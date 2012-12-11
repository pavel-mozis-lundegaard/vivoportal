<?php
namespace Vivo\Indexer\Adapter\Solr;

use Vivo\Indexer\Adapter\Solr\Document as SolrDocument;
use Vivo\Indexer\Adapter\AdapterInterface;
use Vivo\Indexer\QueryHit;
use Vivo\Indexer\Query;
use Vivo\Indexer\Term as IndexTerm;
use Vivo\Indexer\Document;
use Vivo\Indexer\Adapter\Solr\Service as SolrService;

/**
 * Adapter
 * Solr adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * Solr service
     * @var SolrService
     */
    protected $solrService;

    /**
     * Is a transaction open?
     * @var bool
     */
    protected $transaction      = false;

    /**
     * Array of documents to be added within the transaction
     * @var Document[]
     */
    protected $addDocs          = array();

    /**
     * Constructor
     * @param SolrService $solrService
     */
    public function __construct(SolrService $solrService)
    {
        $this->solrService  = $solrService;
    }

    /**
     * Finds documents matching the query in the index and returns an array of query hits
     * If there are no documents found, returns an empty array
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query)
    {
        $solrQuery  = $this->buildSolrQuery($query);
        $result     = $this->solrService->search($solrQuery);
        $numOfHits  = $result->response->numFound;
        $hits           = array();
        for ($i = 0; $i < $numOfHits; $i++) {
            /** @var $solrDoc SolrDocument */
            $solrDoc    = $result->response->docs[$i];
            $doc        = new Document();
            foreach ($solrDoc as $fieldName =>  $fieldValue) {
                $field  = new \Vivo\Indexer\Field($fieldName, $fieldValue);
                $doc->addField($field);
            }
            $hit        = new QueryHit($i, 0, $doc);
            $hits[]     = $hit;
        }
        return $hits;
    }

    /**
     * Deletes documents from the index
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @return void
     */
    public function delete(Query\QueryInterface $query)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Adds a document into the index
     * @param \Vivo\Indexer\Document $doc
     * @return void
     */
    public function addDocument(Document $doc)
    {
        $this->addDocs[]    = $doc;
        if (!$this->isTransactionOpen()) {
            $this->commit();
        }
    }

    /**
     * Optimizes the index
     * @return void
     */
    public function optimize()
    {
        // TODO: Implement optimize() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Returns number of all (undeleted + deleted) documents in the index
     * @return integer
     */
    public function getDocumentCountAll()
    {
        // TODO: Implement getDocumentCountAll() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Returns number of undeleted documents currently present in the index
     * @return integer
     */
    public function getDocumentCountUndeleted()
    {
        // TODO: Implement getDocumentCountUndeleted() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Deletes all documents from index
     * @return void
     */
    public function deleteAllDocuments()
    {
        // TODO: Implement deleteAllDocuments() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    public function begin()
    {
        if ($this->isTransactionOpen()) {
            $this->commit();
        }
        $this->transaction  = true;
    }

    public function commit()
    {
        //Delete documents
//        foreach ($this->deleteIds as $deleteId) {
//            try {
//                $this->index->delete($deleteId);
//            } catch (SearchLucene\Exception\OutOfRangeException $e) {
//                //Document id not found - silently suppress
//            }
//        }
        //Add documents
        foreach ($this->addDocs as $addDoc) {
            $solrDoc    = $this->createSolrDocFromDoc($addDoc);
            $this->solrService->addDocument($solrDoc);
        }
        //Commit changes
        $this->solrService->commit();
        //Reset the transaction
        $this->resetTransaction();
    }

    public function rollback()
    {
        $this->resetTransaction();
    }

    /**
     * Builds and returns a Solr query from Vivo query
     * @param Query\QueryInterface $query
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    protected function buildSolrQuery(Query\QueryInterface $query)
    {
        if ($query instanceof Query\TermInterface) {
            //Term query
            /* @var $query Query\TermInterface */
            $term           = $query->getTerm();
            if ($term->getField()) {
                $solrQuery      = sprintf('%s:"%s"', $term->getField(), $term->getText());
            } else {
                $solrQuery      = $term->getText();
            }
        } elseif ($query instanceof Query\MultiTermInterface) {
            throw new \Exception(sprintf('Multiterm query not implemented', __METHOD__));
            //Multi-term query
            /* @var $query Query\MultiTermInterface */
            $terms          = $query->getTerms();
            $signs          = $query->getSigns();
            $luceneQuery    = new SearchLucene\Search\Query\MultiTerm();
            foreach ($terms as $id => $term) {
                $luceneTerm = new SearchLucene\Index\Term($term->getText(), $term->getField());
                $luceneQuery->addTerm($luceneTerm, $signs[$id]);
            }
        } elseif ($query instanceof Query\WildcardInterface) {
            throw new \Exception(sprintf('Wildcard query not implemented', __METHOD__));
            //Wildcard query
            /* @var $query Query\WildcardInterface */
            $pattern        = $query->getPattern();
            $luceneTerm     = new SearchLucene\Index\Term($pattern->getText(), $pattern->getField());
            $luceneQuery    = new SearchLucene\Search\Query\Wildcard($luceneTerm);
        } elseif ($query instanceof Query\BooleanInterface) {
            throw new \Exception(sprintf('Boolean query not implemented', __METHOD__));
            //Boolean query
            /* @var $query Query\BooleanInterface */
            $subqueries     = $query->getSubqueries();
            $signs          = $query->getSigns();
            $luceneQuery    = new SearchLucene\Search\Query\Boolean();
            foreach ($subqueries as $id => $subquery) {
                $luceneSubquery = $this->buildLuceneQuery($subquery);
                $luceneQuery->addSubquery($luceneSubquery, $signs[$id]);
            }
        } else {
            //Unsupported type of query
            throw new Exception\InvalidArgumentException(sprintf("%s: Unsupported query type '%s'",
                __METHOD__, get_class($query)));
        }
        return $solrQuery;
    }

    /**
     * Returns if a transaction is currently open
     * @return bool
     */
    public function isTransactionOpen()
    {
        return $this->transaction;
    }

    /**
     * Resets transaction - discards any scheduled changes and closes the transaction
     */
    protected function resetTransaction()
    {
//        $this->deleteIds    = array();
        $this->addDocs      = array();
        $this->transaction  = false;
    }

    /**
     * Creates a Solr document from a document
     * @param \Vivo\Indexer\Document $doc
     * @return SolrDocument
     */
    protected function createSolrDocFromDoc(Document $doc)
    {
        $solrDoc    = new SolrDocument();
        /** @var $field \Vivo\Indexer\Field */
        foreach ($doc as $field) {
            $solrDoc->addField($field->getName(), $field->getValue());
        }
        return $solrDoc;
    }
}
