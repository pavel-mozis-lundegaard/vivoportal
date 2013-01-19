<?php
namespace Vivo\Repository;

use Vivo\CMS\Model\Entity;
use Vivo\Indexer\Document;
use Vivo\Indexer\Field;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\Wildcard as WildcardQuery;
use Vivo\Indexer\Query\BooleanOr;
use Vivo\Indexer\Query\Term as TermQuery;
use Vivo\Repository\Exception;
use Vivo\Indexer\FieldHelperInterface as IndexerFieldHelper;
use Vivo\CMS\Model\Document as DocumentModel;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Api\CMS;

use \DateTime;

/**
 * IndexerHelper
 * Generates indexer documents for entities, builds indexer queries based on entity properties
 */
class IndexerHelper
{
    /**
     * Indexer field helper
     * @var IndexerFieldHelper
     */
    protected $indexerFieldHelper;

    /**
     * CMS Api
     * @var CMS
     */
    protected $cms;

    /**
     * Constructor
     * @param \Vivo\Indexer\FieldHelperInterface $indexerFieldHelper
     * @param \Vivo\CMS\Api\CMS $cms
     */
    public function __construct(IndexerFieldHelper $indexerFieldHelper, CMS $cms)
    {
        $this->indexerFieldHelper   = $indexerFieldHelper;
        $this->cms                  = $cms;
    }

    /**
     * Creates an indexer document for the submitted entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @throws Exception\MethodNotFoundException
     * @return \Vivo\Indexer\Document
     */
    public function createDocument(Entity $entity)
    {
        $doc            = new Document();
        $entityClass    = get_class($entity);
        //Fields added by default
        //Published content types
        if ($entity instanceof DocumentModel) {
            $publishedContents  = $this->cms->getPublishedContents($entity);
            if (count($publishedContents) > 0) {
                //There are some published contents
                $publishedContentTypes  = array();
                /** @var $publishedContent Content */
                foreach ($publishedContents as $publishedContent) {
                    $publishedContentTypes[]    = get_class($publishedContent);
                }
                $field  = new Field('\publishedContents', $publishedContentTypes);
                $doc->addField($field);
            }
        }
        //Class field
        $field  = new Field('\class', $entityClass);
        $doc->addField($field);

        //Fields added by metadata config
        $indexerConfigs  = $this->indexerFieldHelper->getIndexerConfig($entityClass);
        foreach ($indexerConfigs as $property => $indexerConfig) {
            $getter = 'get' . ucfirst($property);
            if (!method_exists($entity, $getter)) {
                throw new Exception\MethodNotFoundException(
                    sprintf("%s: Method '%s' not found in '%s'", __METHOD__, $getter, get_class($entity)));
            }
            $value  = $entity->$getter();
            $field  = new Field($indexerConfig['name'], $value);
            $doc->addField($field);
        }
        return $doc;
    }

    /**
     * Builds and returns a query returning a whole subtree of documents beginning at the $entity
     * @param Entity|string $spec Either an Entity object or a path to an entity
     * @throws Exception\InvalidArgumentException
     * @return \Vivo\Indexer\Query\BooleanInterface
     */
    public function buildTreeQuery($spec)
    {
        if (is_string($spec)) {
            $path   = $spec;
        } elseif ($spec instanceof Entity) {
            /** @var $spec Entity */
            $path   = $spec->getPath();
        } else {
            throw new Exception\InvalidArgumentException(sprintf('%s: Unsupported specification type', __METHOD__));
        }
        $entityTerm          = new IndexerTerm($path, '\path');
        $entityQuery         = new TermQuery($entityTerm);
        $descendantPattern   = new IndexerTerm($path . '/*', '\path');
        $descendantQuery     = new WildcardQuery($descendantPattern);
        $boolQuery           = new BooleanOr($entityQuery, $descendantQuery);
        return $boolQuery;
    }

    /**
     * Builds a term identifying the $entity
     * @param \Vivo\CMS\Model\Entity $entity
     * @return \Vivo\Indexer\Term
     */
    public function buildEntityTerm(Entity $entity)
    {
        $term   = new IndexerTerm($entity->getUuid(), '\uuid');
        return $term;
    }
}