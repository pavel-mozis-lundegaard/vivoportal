<?php
namespace Vivo\Backend\UI;

use Vivo\UI\ComponentContainer;

class Backend extends ComponentContainer
{

    protected $routeMatch;

    protected $sm;


    protected $modules = array (
            'explorer' => 'Vivo\Backend\UI\Explorer\Explorer',
        );

    public function setModuleComponent($component)
    {
        $this->addComponent($component, 'module');
    }
}
