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
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param \Vivo\CMS\Api\Indexer $indexerApi
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     */
    public function __construct(CMS $cmsApi, IndexerApi $indexerApi, SiteEvent $siteEvent)
    {
        $this->cmsApi       = $cmsApi;
        $this->indexerApi   = $indexerApi;
        $this->siteEvent    = $siteEvent;
    }

    public function init()
    {
        $this->view->children = $this->getDocuments();
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
            $items = $this->content->getOverviewItems();
            foreach ($items as $item) {
                $documents[] = $this->cmsApi->getSiteEntity($item, $this->siteEvent->getSite());
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
