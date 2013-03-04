<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\Api\CMS;

use Vivo\UI\Component;

/**
 * Component show preview of current document in iframe.
 */
class Viewer extends Component
{
    /**
     * @var CMS
     */
    protected $cmsApi;

    /**
     * @param CMS $cmsApi
     */
    public function __construct(CMS $cmsApi)
    {
        $this->cmsApi = $cmsApi;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $entity = $this->getParent()->getEntity();
        $this->getView()->entityPath = $this->cmsApi->getEntityRelPath($entity);
        return parent::view();
    }
}
