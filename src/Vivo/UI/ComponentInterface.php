<?php
namespace Vivo\UI;

/**
 * Interface for UI components.
 */
interface ComponentInterface
{
//    /**
//     * @return void
//     */
//    public function init();

//    /**
//     * Returns view model or string to display directly
//     * @return \Zend\View\Model\ModelInterface|string
//     */
//    public function view();

    /**
     * Returns view model of the Component or string to display directly
     * @return \Zend\View\Model\ModelInterface|string
     */
    public function getView();

//    /**
//     * @return void
//     */
//    public function done();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return ComponentContainerInterface
     */
    public function getParent();

    /**
     * Returns component Event Manager
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager();

    /**
     * Returns component Event
     * @return ComponentEventInterface
     */
    public function getEvent();
}
