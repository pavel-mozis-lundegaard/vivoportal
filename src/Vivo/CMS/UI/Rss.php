<?php
namespace Vivo\CMS\UI;

use Vivo\CMS\Api;

class Rss extends Component
{
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    private $items = array();

    public function __construct(Api\CMS $cmsApi, Api\Document $documentApi)
    {
        $this->cmsApi = $cmsApi; //@deprecated
        $this->documentApi = $documentApi;
    }

    public function init()
    {
        foreach ($this->documentApi->getChildDocuments($this->document) as $child) {
            if($this->documentApi->isPublished($child)) {
                $this->items[] = $child;
            }
        }
    }

    public function view()
    {
        //TODO: base URL

        $view = parent::view();
        $view->site = $this->cmsEvent->getSite();
        $view->items = $this->items;

        return $view;
    }
}
