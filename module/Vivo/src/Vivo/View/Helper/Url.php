<?php
namespace Vivo\View\Helper;

use Zend\Mvc\ModuleRouteListener;

/**
 * Url view helper.
 *
 * Has a same function as \Zend\View\Helper\Url, it only modifies reused params.
 * Helper always reuse 'path' and 'host' router match param
 * and never reuse 'constroller' param.
 *
 */
class Url extends \Zend\View\Helper\Url
{
    public function __invoke($name = null, array $params = array(),
            $options = array(), $reuseMatchedParams = false)
    {
        if (null === $this->router) {
            throw new Exception\RuntimeException('No RouteStackInterface instance provided');
        }

        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = array();
        }

        if ($name === null) {
            if ($this->routeMatch === null) {
                throw new Exception\RuntimeException('No RouteMatch instance provided');
            }

            $name = $this->routeMatch->getMatchedRouteName();

            if ($name === null) {
                throw new Exception\RuntimeException('RouteMatch does not contain a matched route name');
            }
        }

        if ($reuseMatchedParams && $this->routeMatch !== null) {
            $routeMatchParams = $this->routeMatch->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $options['name'] = $name;

        //modify reused params for asembling routes.
        //we don't want to resuse 'controller' param and
        //we always want to reuse host and path params
        unset($params['controller']);
        if (!isset($params['host']))
            $params['host'] =  $this->routeMatch->getParam('host');
        if (!isset($params['path']))
            $params['path'] = $this->routeMatch->getParam('path');

        $url = $this->router->assemble($params, $options);
        $url = str_replace('%2F', '/', $url);
        return $url;
    }
}
