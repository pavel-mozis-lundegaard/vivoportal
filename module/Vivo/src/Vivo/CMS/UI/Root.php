<?php
namespace Vivo\CMS\UI;

use Vivo\UI;
use Vivo\UI\ComponentInterface;

/**
 * Root component of the UI component tree.
 */
class Root extends Component
{

    const MAIN_COMPONENT_NAME = 'main';
    const COMPONENT_NAME = 'root';

    /**
     * Sets main UI component
     * @param ComponentInterface $component
     */
    public function setMain(ComponentInterface $component)
    {
        $this->addComponent($component, self::MAIN_COMPONENT_NAME);
        $this->setName(self::COMPONENT_NAME);
    }

    public function view()
    {
        return $this->getComponent(self::MAIN_COMPONENT_NAME)->view();
    }
}
