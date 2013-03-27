<?php
namespace Vivo\View\Helper;

use Vivo\UI\Component;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for getting action url
 */
class ActionUrl extends AbstractHelper
{

    public function __invoke($action, array $queryArgs = array(),  $reuseMatchedParams = false)
    {
        $actionHelper   = $this->view->plugin('action');
        $act            = $actionHelper($action);
        $options        = array(
            'query' => array(
                'act' => $act,
                'args' => $queryArgs,
            ),
        );
        $urlParams  = array();
        $urlHelper  = $this->getView()->plugin('url');
        $url        = $urlHelper(null, $urlParams, $options, $reuseMatchedParams);
        return $url;
    }
}
