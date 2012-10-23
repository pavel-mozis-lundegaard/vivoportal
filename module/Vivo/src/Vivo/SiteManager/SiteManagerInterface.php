<?php
namespace Vivo\SiteManager;

use Zend\EventManager\EventsCapableInterface;
use Zend\Mvc\Router\RouteMatch;

/**
 * SiteManagerInterface
 */
interface SiteManagerInterface extends EventsCapableInterface
{
    /**
     * Sets the RouteMatch object
     * @param RouteMatch|null $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch = null);

    /**
     * Bootstraps the SiteManager
     */
    public function bootstrap();

    /**
     * Prepares the site
     */
    public function prepareSite();
}