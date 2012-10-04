<?php
namespace Vivo\UI;
/**
 * @author kormik
 *
 */
interface ComponentContainerInterface {
	public function addComponent(ComponentInterface $component, $name);
	public function removeComponent($name);
	public function getComponent($name);
}
