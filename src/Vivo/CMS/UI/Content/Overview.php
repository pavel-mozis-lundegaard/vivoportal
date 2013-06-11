<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Content\Overview as OverviewModel;
use Vivo\CMS\UI\Component;
use Vivo\CMS\UI\Exception\Exception;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\CMS\Api\Indexer as IndexerApi;
use Vivo\CMS\RefInt\SymRefConvertorInterface;
use Vivo\CMS\Model\Entity;
use Vivo\Repository\Exception\EntityNotFoundException;
use Vivo\CMS\UI\Exception\RuntimeException;

use Zend\Cache\Storage\StorageInterface as Cache;

/**
 * Overview UI component
 *
 * Overview displays list of subpages (sub-documents) or other designated
 * documents. Typically is used to create reports and menus.
 */

class Overview extends Component
{

    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    /**
     * Indexer API
     * @var IndexerApi
     */
    protected $indexerApi;

    /**
     * @var SiteEvent
     */
    private $siteEvent;

    /**
     * @var array of \Vivo\CMS\Model\Document
     */
    protected $children = array();

    /**
     * Cache for overview documents
     * @var Cache
     */
    protected $cache;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param \Vivo\CMS\Api\Indexer $indexerApi
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     * @param \Zend\Cache\Storage\StorageInterface $cache
     */
    public function __construct(CMS $cmsApi, IndexerApi $indexerApi, SiteEvent $siteEvent, Cache $cache = null)
    {
        $this->cmsApi       = $cmsApi;
        $this->indexerApi   = $indexerApi;
        $this->siteEvent    = $siteEvent;
        $this->cache        = $cache;
    }

    public function init()
    {
        if ($this->cache) {
            $key        = $this->getCacheKey();
            $success    = null;
            $children   = $this->cache->getItem($key, $success);
            if (!$success) {
                $children  = $this->getDocuments();
                $this->cache->setItem($key, $children);
            }

        } else {
            $children   = $this->getDocuments();
        }
        $this->view->children = $children;
    }

    /**
     * Returns cache key used to cache the overview documents
     * @return string
     * @throws \Vivo\CMS\UI\Exception\RuntimeException
     */
    protected function getCacheKey()
    {
        /** @var $overviewModel \Vivo\CMS\Model\Content\Overview */
        $overviewModel  = $this->content;
        switch ($overviewModel->getOverviewType()) {
            case \Vivo\CMS\Model\Content\Overview::TYPE_DYNAMIC:
                if (is_null($overviewModel->getOverviewPath())) {
                    //Overview path not specified, use the current requested doc
                    $overviewPath = $this->cmsEvent->getRequestedPath();
                } else {
                    //Overview path specified
                    $overviewPath = $overviewModel->getOverviewPath();
                }
                $keyParts   = array(
                    'requested_path'    => $this->cmsEvent->getRequestedPath(),
                    'overview_path'     => $overviewPath,
                    'overview_criteria' => $overviewModel->getOverviewCriteria(),
                    'overview_sorting'  => $overviewModel->getOverviewSorting(),
                    'overview_limit'    => $overviewModel->getOverviewLimit(),
                );
                $key    = sha1(implode(',', $keyParts));
                break;
            case \Vivo\CMS\Model\Content\Overview::TYPE_STATIC:
                $concat = '';
                foreach ($overviewModel->getOverviewItems() as $docPath) {
                    $concat .= $docPath;
                }
                $key    = sha1($concat);
                break;
            default:
                throw new RuntimeException(sprintf("%s: Unsupported overview type '%s'",
                    __METHOD__, $overviewModel->getType()));
                break;
        }
        return $key;
    }

    /**
     * Returns documents to list in overview.
     *
     * @throws Exception
     * @return \Vivo\CMS\Model\Document[]
     */
    public function getDocuments()
    {
        $documents = array();
        $type = $this->content->getOverviewType();
        if ($type == OverviewModel::TYPE_DYNAMIC) {
            if ($path = $this->content->getOverviewPath()) {
                $path = $this->cmsApi->getEntityAbsolutePath($path, $this->siteEvent->getSite());
            } else {
                $path = $this->document->getPath();
            }

            $query = $this->createQuery($path, $this->content->getOverviewCriteria());

            $params = array();
            if ($limit = $this->content->getOverviewLimit()) {
                $params['page_size'] = $limit;
            }
            if ($sort = $this->content->getOverviewSorting())
            {
                $params['sort'] = $sort;
            }

            $documents = $this->indexerApi->getEntitiesByQuery($query, $params);

        } elseif ($type == OverviewModel::TYPE_STATIC) {
            $items  = $this->content->getOverviewItems();
            $site   = $this->siteEvent->getSite();
            foreach ($items as $item) {
                $document = $this->cmsApi->getSiteEntity($item, $site);
                if ((bool) $document->getAllowListing() === true) {
                    $documents[] = $document;
                }
            }
        } else {
            throw new Exception(sprintf('%s: Unsupported overview type `%s`.', __DIR__, $type));
        }
        return $documents;
    }

    /**
     * Assembles string query for indexer.
     *
     * Searches only for published documents.
     *
     * @param string $path Absolute path of document.
     * @param string $criteria Indexer query.
     * @return string
     */
    protected function createQuery($path, $criteria)
    {
        $query = '\path:"'. $path . '/*" ';          
        $query .= ' AND \class:"Vivo\CMS\Model\Document"';
        $query .= ' AND \publishedContents:"*"';  // search only documents with published content          
        $query .= ' AND \allowListingInOverview:"1"';
        if ($criteria) {
            $criteria   = $this->makePathsAbsolute($criteria);
            $query .= " AND ($criteria)";
        } else {
            $query .= ' AND NOT \path:"' . $path . '/*/*" '; //exclude sub-documents
        }
        return $query;
    }

    /**
     * Converts any relative path found in the string to absolute path
     * @param string $stringWithPaths
     * @return string
     */
    protected function makePathsAbsolute($stringWithPaths)
    {
        $re     = '/(\.|)(' . SymRefConvertorInterface::PATTERN_URL . ')(\*)?/';
        $cmsApi = $this->cmsApi;
        $site   = $this->siteEvent->getSite();
        $callback   = function(array $matches) use ($cmsApi, $site) {
            $url    = $matches[2];
            if (isset($matches[3])) {
                $wildcard   = true;
            } else {
                $wildcard   = false;
            }
            try {
                /** @var $doc Entity */
                $doc        = $cmsApi->getSiteEntity($url, $site);
                $absPath    = $doc->getPath();
                if ($wildcard) {
                    //This is a fix for URLs with wildcard e.g. /cs/about-us/*
                    //If the trailing wildcard was not matched and added, such relative URL would be converted to
                    // SiteName/ROOT/cs/about-us* (note the missing slash) and it would match also the /cs/about-us/
                    //document in the indexer which was not intended - only its children were requested
                    $absPath    .= '/*';
                }
            } catch (EntityNotFoundException $e) {
                $absPath    = $url;
            }
            return $absPath;
        };
        $converted  = preg_replace_callback($re, $callback, $stringWithPaths);
        return $converted;
    }
}
