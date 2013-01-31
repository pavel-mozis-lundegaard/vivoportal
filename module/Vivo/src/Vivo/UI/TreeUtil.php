<?php
namespace Vivo\UI;
use Vivo\UI\Exception\LogicException;

use Vivo\CMS\UI\Component;

use Vivo\UI\Exception\ExceptionInterface as UIException;
use Vivo\UI\Exception\RuntimeException;

/**
 * Performs operation on UI component tree.
 */
class TreeUtil
{
    /**
     * @var ComponentInterface
     */
    private $root;

    /**
     * @param ComponentInterface $root
     */
    public function setRoot(ComponentInterface $root)
    {
        $this->root = $root;
    }

    public function getRoot()
    {
        if (null === $this->root) {
            throw new LogicException(
                sprintf('%s: Root component is not set.', __METHOD__));
        }
        return $this->root;
    }

    public function getComponent($path)
    {
        $component = $this->getRoot();
        $names = explode(Component::COMPONENT_SEPARATOR, $path);
        for ($i = 1; $i < count($names); $i++) {
            try {
                $component = $component->getComponent($names[$i]);
            } catch (UIException $e) {
                throw new RuntimeException(
                    sprintf("%s: Component for path '%s' not found.",
                        __METHOD__, $path), null, $e);
            }
        }
        return $component;
    }

    public function InitTree()
    {
        //TODO
    }

    public function initComponent($path)
    {
        //TODO
    }

    public function invokeAction($path, $action, $params)
    {
        if (substr($action, 0, 2) == '__' || $action == 'init') // Init and php magic methods are not accessible.
            throw new RuntimeException("Method $action is not accessible");
        $component = $this->getComponent($path);

        if (!method_exists($component, $action)) {
            throw new RuntimeException(
                sprintf("%s: Component '%s' doesn't have method '%s'.",
                    __METHOD__, get_class($component), $action));
        }

        try {
            return call_user_func_array(array($component, $action), $params);
        } catch (UIException $e) {
            throw new RuntimeException(
                sprintf("%s: Can not call action on component.", __METHOD__),
                null, $e);
        }
    }

}
