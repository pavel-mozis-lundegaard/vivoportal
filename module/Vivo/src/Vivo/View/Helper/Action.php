<?php
namespace Vivo\View\Helper;

use Vivo\UI\Component;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * View helper for gettting action url
 */
class Action extends AbstractHelper
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

    public function __invoke($action, $params = array())
    {
        $model = $this->view->getCurrentModel();
        $act = $model->getVariable('cpath') . Component::COMPONENT_SEPARATOR
            . $action;
        return $this->urlHelper
            ->__invoke('vivo/cms/query', array('act' => $act, 'args' => $params), true);
    }
}
