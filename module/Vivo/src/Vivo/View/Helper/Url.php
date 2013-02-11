<?php
namespace Vivo\View\Helper;



class Url extends \Zend\View\Helper\Url
{
    public function __invoke($name = null, array $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (!isset($params['host']))
            $params['host'] =  $this->routeMatch->getParam('host');

        if (!isset($params['path']))
            $params['path'] = $this->routeMatch->getParam('path');

         if (3 == func_num_args()) {
             return parent::__invoke($name, $params, $options);
         } else {
             return parent::__invoke($name, $params, $options, $reuseMatchedParams);
         }



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
//                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $options['name'] = $name;

        return $this->router->assemble($params, $options);
    }
}