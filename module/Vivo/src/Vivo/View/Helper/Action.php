<?php
namespace Vivo\View\Helper;

use Vivo\UI\Component;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for gettting action url
 */
class Action extends AbstractHelper
{
    public function __invoke($action, $params = array())
    {
        $model = $this->view->plugin('view_model')->getCurrent();
        $component = $model->getVariable('component');
        return $component['path'] . Component::COMPONENT_SEPARATOR . $action;
    }
}
