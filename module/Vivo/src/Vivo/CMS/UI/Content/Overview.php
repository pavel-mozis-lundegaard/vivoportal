<?php
namespace Vivo\CMS\UI\Content;

use Vivo\SiteManager\Event\SiteEvent;

use Vivo\CMS\CMS;
use Vivo\CMS\Model\Content\Overview as OverviewModel;
use Vivo\CMS\UI\Component;

class Overview extends Component
{

    /**
     * @var \Vivo\CMS\CMS
     */
    private $cms;

    /**
     * @var SiteEvent
     */
    private $siteEvent;

    /**
     * @var array of \Vivo\CMS\Model\Document
     */
    protected $children = array();

    public function __construct(CMS $cms, SiteEvent $siteEvent)
    {
        parent::__construct();
        $this->cms = $cms;
        $this->siteEvent = $siteEvent;
    }

    public function init()
    {
        $this->view->children = $this->getDocuments();
    }

    public function getDocuments()
    {
        $documents = array();
        $type = $this->content->getOverviewType();
        if ($type == OverviewModel::TYPE_DYNAMIC) {
            if ($this->content->getOverviewPath() != '') {
                $document = $this->cms->getSiteDocument($this->content->getOverviewPath(), $this->siteEvent->getSite());
            } else {
                $document = $this->document;
            }
            $documents = $this->cms->getChildDocuments($document);
        } elseif ($type == OverviewModel::TYPE_STATIC) {
            $items = $this->content->getOverviewItems();
            foreach ($items as $item) {
                $documents[] = $this->cms->getSiteDocument($item, $this->siteEvent->getSite());
            }
        } else {
            throw new \Exception('Bad overview type.');
        }

        return $documents;
    }
}
