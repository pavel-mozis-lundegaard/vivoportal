<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\UI\Component as CMSComponent;

use Zend\Di\Di;

class Component extends CMSComponent
{
    protected $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function init()
    {
        $component = $di->newInstance($this->content->getFrontComponent());
        $component->init();
        $this->addComponent($component, 'main');
    }
}