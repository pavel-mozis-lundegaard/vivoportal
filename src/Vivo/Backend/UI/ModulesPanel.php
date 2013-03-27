<?php
namespace Vivo\Backend\UI;

use Vivo\CMS\UI\Component;

/**
 * Panel for selecting backend module.
 */
class ModulesPanel extends Component
{

    /**
     * @var \Vivo\Backend\ModuleResolver
     */
    private $moduleResolver;

    /**
     * @param \Vivo\Backend\ModuleResolver $moduleResolver
     */
    public function __construct(\Vivo\Backend\ModuleResolver $moduleResolver)
    {
        $this->moduleResolver = $moduleResolver;
    }

    /**
     * Initialize.
     */
    public function init()
    {
        $this->view->modules = $this->moduleResolver->getModules();
    }
}
