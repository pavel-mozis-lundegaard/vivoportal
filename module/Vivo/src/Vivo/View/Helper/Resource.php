<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Model\Entity;

use Vivo\UI\Component;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * View helper for gettting action url
 */
class Resource extends AbstractHelper
{
    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @param Url $urlhelper
     */
    public function __construct(Url $urlhelper)
    {
        $this->urlHelper = $urlhelper;
    }

    public function __invoke($resourcePath, $source)
    {
        if ($source instanceof Entity) {
            $entityUrl = $this->cms->getEntityUrl($source);
        }

        //TODO check if resource exists
        $model = $this->view->getCurrentModel();
        return $this->urlHelper
            ->__invoke('vivo/resource', array('module' => $source, 'path' => $resourcePath, 'entityUrl' => $entityUrl));
    }
}
