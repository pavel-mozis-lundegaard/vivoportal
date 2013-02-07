<?php
namespace Vivo\UI;

/**
 * Class that implements this interface could contains child components.
 */
interface ComponentContainerInterface
{
    public function addComponent(ComponentInterface $component, $name);
    public function removeComponent($name);
    public function getComponent($name);
    public function getComponents();
}
