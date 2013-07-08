<?php
namespace Vivo\CMS\UI;

use Vivo\UI;
use Vivo\UI\ComponentInterface;
use Vivo\UI\ComponentEventInterface;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Root component of the UI component tree.
 */
class Root extends Component
{

    const MAIN_COMPONENT_NAME   = 'main';
    const COMPONENT_NAME        = 'root';

    /**
     * Sets main UI component
     * @param ComponentInterface $component
     */
    public function setMain(ComponentInterface $component)
    {
        $this->addComponent($component, self::MAIN_COMPONENT_NAME);
        $this->setName(self::COMPONENT_NAME);
    }

    /**
     * Returns view model of the Component or string to display directly
     * @return \Zend\View\Model\ModelInterface|string
     */
    public function getView()
    {
        return $this->getComponent(self::MAIN_COMPONENT_NAME)->getView();
    }

    /**
     * Attaches listeners
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function attachListeners(ServiceLocatorInterface $serviceLocator)
    {
        //Override attached listeners from Component and ComponentContainer
        //This Root component does not use its own view model but rather uses the view model of its main component
        //(see getView(). Therefore leaving the default listeners from Component and ComponentContainer attached
        //results in mixed-up view models
    }
}
