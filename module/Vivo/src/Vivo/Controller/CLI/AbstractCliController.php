<?php
namespace Vivo\Controller\CLI;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Abstract controller for CLI controllers
 */
abstract class AbstractCliController extends AbstractActionController
{
    const COMMAND = '';

    public function notFoundAction()
    {
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        return sprintf("Unknown subcommand '%s'\nUsage:\n %s",
                       $routeMatch->getParam('action'),
                       $this->getConsoleUsage());
    }

    abstract public function getConsoleUsage();

    public function helpAction()
    {
        return $this->getConsoleUsage();
    }
}
