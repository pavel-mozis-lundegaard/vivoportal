<?php
namespace Vivo\Controller\CLI;

/**
 * Vivo CLI controller for command 'module'
 */
class ModuleController extends AbstractCliController
{
    const COMMAND = 'module';

    public function defaultAction()
    {
        return $this->listAction();
    }

    public function getConsoleUsage()
    {
        return 'module usage: ...';
    }

    public function installAction()
    {
        //TODO
    }

    public function removeAction()
    {
        //TODO
    }

    public function listAction()
    {
        //TODO
        return "Instaled modules:\n module one\n module two\n ...";
    }

}
