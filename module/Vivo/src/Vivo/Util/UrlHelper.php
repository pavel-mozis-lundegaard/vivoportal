<?php
namespace Vivo\Util;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface;

/**
 * Helper class for asembling urls.
 *
 * @see \Zend\Mvc\Controller\Plugin\Url
 */
class UrlHelper
{

    /**
     * @var RouteMatch
     */
    private $routeMatch;

    /**
     * @var RouteStackInterface
     */
    private $router;

    /**
     * Constructor.
     * @param RouteStackInterface $router
     * @param RouteMatch $routeMatch
     */
    public function __construct(RouteStackInterface $router, RouteMatch $routeMatch = null)
    {
        $this->router = $router;
        $this->routeMatch = $routeMatch;
    }

    /**
     * Asemble url using route and router params.
     *
     * @param string $route
     * @param array $params
     * @param mixed $options
     * @param boolean $reuseMatchedParams
     * @return string
     * @throws \RuntimeException
     */
    public function fromRoute($route = null, array $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = array();
        }

        if ($route === null) {
            if (!$this->routeMatch) {
                throw new \RuntimeException('No RouteMatch instance present');
            }

            $route = $this->routeMatch->getMatchedRouteName();

            if ($route === null) {
                throw new \RuntimeException('RouteMatch does not contain a matched route name');
            }
        }

        if ($reuseMatchedParams && $this->routeMatch) {
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

        $options['name'] = $route;
        return $this->router->assemble($params, $options);
    }
}
