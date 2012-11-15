<?php
namespace Vivo\Indexer\Adapter;

use Vivo\Indexer\Query;
use Vivo\Indexer\Exception;
use Vivo\Indexer\QueryHit;
use Vivo\Indexer\Document;
use Vivo\Indexer\Field;
Use Vivo\Indexer\Term as IndexTerm;

use ZendSearch\Lucene as SearchLucene;

/**
 * Lucene
 * Lucene adapter
 */
class Lucene implements AdapterInterface
{
    /**
     * Lucene index
     * @var SearchLucene\SearchIndexInterface
     */
    protected $index;

    /**
     * Constructor
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     */
    public function __construct(SearchLucene\SearchIndexInterface $index)
    {
        $this->index    = $index;
    }

    /**
     * Finds documents matching the query in the index and returns an array of query hits
     * If there are no documents found, returns an empty array
     * @param \Vivo\Indexer\Query\QueryInterface $query
     * @throws \Vivo\Indexer\Exception\InvalidArgumentException
     * @return QueryHit[]
     */
    public function find(Query\QueryInterface $query)
    {
        $luceneQuery    = $this->buildLuceneQuery($query);
        $luceneHits     = $this->index->find($luceneQuery);
        $hits           = array();
        if (count($luceneHits) > 0) {
            foreach ($luceneHits as $luceneHit) {
                /* @var $luceneHit SearchLucene\Search\QueryHit */
                $luceneDoc  = $luceneHit->getDocument();
                $fieldNames = $luceneDoc->getFieldNames();
                $doc        = new Document();
                foreach ($fieldNames as $fieldName) {
                    $luceneField    = $luceneDoc->getField($fieldName);
                    $field          = new Field($luceneField->name, $luceneField->value,
                                                $luceneField->isStored, $luceneField->isIndexed,
                                                $luceneField->isTokenized, $luceneField->isBinary);
                    $doc->addField($field);
                }
                $hit    = new QueryHit(
                                (string)$luceneHit->id,
                                (string)$luceneHit->document_id,
                                $luceneHit->score,
                                $doc);
                $hits[] = $hit;
            }
        }
        return $hits;
    }

    /**
     * Builds and returns a Lucene query from Vivo query
     * @param Query\QueryInterface $query
     * @return SearchLucene\Search\Query\AbstractQuery
     * @throws Exception\InvalidArgumentException
     */
    protected function buildLuceneQuery(Query\QueryInterface $query)
    {
        if ($query instanceof Query\TermInterface) {
            //Term query
            /* @var $query Query\TermInterface */
            $term           = $query->getTerm();
            $luceneTerm     = new SearchLucene\Index\Term($term->getText(), $term->getField());
            $luceneQuery    = new SearchLucene\Search\Query\Term($luceneTerm);
        } elseif ($query instanceof Query\MultiTermInterface) {
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
            //Wildcard query
            /* @var $query Query\WildcardInterface */
            $pattern        = $query->getPattern();
            $luceneTerm     = new SearchLucene\Index\Term($pattern->getText(), $pattern->getField());
            $luceneQuery    = new SearchLucene\Search\Query\Wildcard($luceneTerm);
        } elseif ($query instanceof Query\BooleanInterface) {
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
        return $luceneQuery;
    }

    public function begin()
    {
        // TODO: Implement begin() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    public function commit()
    {
        // TODO: Implement commit() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    public function rollback()
    {
        // TODO: Implement rollback() method.
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Finds documents based on a term
     * This is usually faster than find()
     * Returns an array of document ids
     * @param IndexTerm $term
     * @return array
     */
    public function termDocs(IndexTerm $term)
    {
        $luceneTerm     = new SearchLucene\Index\Term($term->getText(), $term->getField());
        $docIds         = $this->index->termDocs($luceneTerm);
        return $docIds;
    }
}