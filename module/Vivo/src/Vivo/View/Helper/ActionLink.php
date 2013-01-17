<?php
namespace Vivo\View\Helper;

use Vivo\UI\Component;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * View helper for gettting action url
 */
class ActionLink extends AbstractHelper
{

    public function __invoke($action, $body,  $params = array())
    {
        $model = $this->getView()->getCurrentModel();
        $component = $model->getVariable('component');

        $act =  $component['path'] . Component::COMPONENT_SEPARATOR
            . $action;
        $urlHelper =  $this->getView()->plugin('url');

        $url = $urlHelper('vivo/cms/query', array('act' => $act, 'args' => $params), true);
        $link = "<a href='$url'>$body</a>";
        return $link;
    }
}
