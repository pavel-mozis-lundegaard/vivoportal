<?php
namespace Vivo\Controller\CLI;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Vivo CLI controller for command 'info'
 */
class InfoController extends AbstractCliController
{
    public function defaultAction() {
        return $this->versionAction();
    }

    public function versionAction() {
        return "Vivo 2.0 Community Project";
    }

    public function getConsoleUsage () {
        //TODO return usage info
        return 'info usage: ...';
    }
}
