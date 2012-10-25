<?php
namespace Vivo\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

use Zend\EventManager\EventManagerInterface;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 * @author kormik
 */
class CMSFrontController implements DispatchableInterface,
    InjectApplicationEventInterface, ServiceLocatorAwareInterface
{

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceLocator;

    /**
     * @var EventManagerInterface     */
    protected $events;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function dispatch(Request $request,
        Response $response = null)
    {
        //TODO find document in repository and return it
        $path = $this->event->getRouteMatch()->getParam('path');
        $host = $this->event->getRouteMatch()->getParam('host');

        //Install manager testing
        $moduleStorage  = $this->serviceLocator->get('module_storage');
        $config         = $this->serviceLocator->get('config');
        $storageFactory = $this->serviceLocator->get('storage_factory');
        $modulePaths    = $config['vivo']['modules']['module_paths'];
        $ioUtil         = new \Vivo\IO\IOUtil();
        $storageUtil    = new \Vivo\Storage\StorageUtil($ioUtil);
        $installMgr     = new \Vivo\Module\InstallManager\InstallManager($moduleStorage, $modulePaths, '/', $storageUtil, $storageFactory);

        \Zend\Debug\Debug::dump($installMgr->getModules());
        \Zend\Debug\Debug::dump($installMgr->moduleExists('Gvm1'), 'Gvm1 exists');
        \Zend\Debug\Debug::dump($installMgr->moduleExists('Baz'), 'Baz exists');

        $moduleUrl  = 'file://c:\Work\DummyModules\Vm10';

        $installMgr->addModule($moduleUrl);
        //END Install manager testing

        $response->setContent('CMS document for path: ' . $path);
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);
        return $response;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return \Zend\Mvc\MvcEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set the event manager instance
     * @param  EventManagerInterface $events
     * @return CMSFrontController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events
            ->setIdentifiers(
                array(__CLASS__, get_called_class(), 'cms_front_controller',));
        $this->events = $events;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * Retrieve the event manager
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * Register the default event listeners
     * @return CMSFrontController
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events
            ->attach(ModuleEvent::EVENT_LOAD_MODULES,
                array($this, 'onLoadModules'));
    }

}
