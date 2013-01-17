<?php
namespace Vivo\Controller\CLI;

/**
 * Vivo CLI controller for command 'indexer'
 */
class IndexerController extends AbstractCliController
{
    const COMMAND = 'indexer';

    public function getConsoleUsage()
    {
        return 'indexer usage: ...';
    }


    public function clearAction()
    {

    }
}
