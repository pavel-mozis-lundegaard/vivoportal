<?php
namespace Vivo\View\Helper;

/**
 * Url view helper.
 *
 * Has the same function as \Zend\View\Helper\Url, it only modifies reused params.
 * Helper always reuses 'path' and 'host' router match param and never reuses 'controller' param.
 */
class Url extends \Zend\View\Helper\AbstractHelper
{
    protected $urlHelper;

    public function __construct($urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    public function __invoke($name = null, array $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        return $this->urlHelper->fromRoute($name, $params, $options, $reuseMatchedParams);
    }
}
