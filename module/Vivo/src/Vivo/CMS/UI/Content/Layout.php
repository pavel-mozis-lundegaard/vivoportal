<?php
namespace Vivo\CMS\UI\Content;

use Vivo\UI\ComponentInterface;
use Vivo\UI\ComponentContainer;
use Vivo\CMS\UI\Component;

/**
 * Layout UI component wrap the underlaying component to layout.
 */
class Layout extends Component
{

    const MAIN_COMPONENT_NAME = 'param';

    public function setMain(ComponentInterface $component)
    {
        $this->addComponent($component, self::MAIN_COMPONENT_NAME);
    }

    public function createPanels()
    {
        //TODO
    }
}
