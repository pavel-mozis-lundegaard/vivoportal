<?php
namespace Vivo\UI;

/**
 * Class that implements this interface could contains child components.
 */
interface ComponentContainerInterface
{
    public function addComponent(ComponentInterface $component, $name);
    public function removeComponent($name);

    /**
     * Returns component by name
     * @param string $name
     * @return ComponentInterface
     */
    public function getComponent($name);

    public function getComponents();

    /**
     * Returns if the container contains component with the specified name
     * @param string $name
     * @return bool
     */
    public function hasComponent($name);
}
