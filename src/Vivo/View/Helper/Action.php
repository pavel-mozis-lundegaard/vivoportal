<?php
namespace Vivo\View\Helper;

use Vivo\UI\Component;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for getting action url
 */
class Action extends AbstractHelper
{
    public function __invoke($action)
    {
        $model      = $this->view->plugin('view_model')->getCurrent();
        $component  = $model->getVariable('component');
        $act        = $component['path'] . Component::COMPONENT_SEPARATOR . $action;
        return $act;
    }
}
