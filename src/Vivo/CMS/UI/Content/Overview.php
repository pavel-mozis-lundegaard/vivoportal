<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Content\Overview as OverviewModel;
use Vivo\CMS\UI\Component;
use Vivo\CMS\UI\Exception\Exception;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\CMS\Api\Indexer as IndexerApi;

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
    private $cms;

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
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\CMS\Api\Indexer $indexerApi
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     */
    public function __construct(CMS $cms, IndexerApi $indexerApi, SiteEvent $siteEvent)
    {
        $this->cms          = $cms;
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
                $path = $this->cms->getEntityAbsolutePath($path, $this->siteEvent->getSite());
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
                $documents[] = $this->cms->getSiteEntity($item, $this->siteEvent->getSite());
            }
        } else {
            throw new Exception(sprintf('%s: Unsupported overview type `%s`.', __DIR__, $type));
        }
        return $documents;
    }

    /**
     * Asembles string query for indexer.
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
            $query .= " AND ($criteria)";
        } else {
            $query .= ' AND NOT \path:"' . $path . '/*/*" '; //exlude sub-documents
        }
        return $query;
    }
}
