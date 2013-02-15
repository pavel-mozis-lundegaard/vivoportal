<?php
namespace Vivo\Backend;

use Vivo\CMS\Model\Site;
use Vivo\Controller\Exception;
use Vivo\IO\InputStreamInterface;
use Vivo\Security\Manager\AbstractManager;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\UI\Component;
use Vivo\UI\ComponentTreeController;
use Vivo\Util\Redirector;

use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 */
class BackendController implements DispatchableInterface,
    InjectApplicationEventInterface, ServiceManagerAwareInterface
{

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    protected $mvcEvent;

    /**
     * @var SiteEvent
     */
    protected $siteEvent;

    /**
     * @var \Vivo\UI\ComponentTreeController
     */
    protected $tree;

    /**
     *
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Security manager.
     * @var AbstractManager
     */
    protected $securityManager;


    /**
     * Constructor.
     * @param AbstractManager $securityManager
     */
    public function __construct(\Vivo\CMS\Security\Manager\AbstractManager $securityManager)
    {
        $this->securityManager = $securityManager;
    }

    /**
     * @param Site $site
     */
    public function setSiteEvent(SiteEvent $siteEvent)
    {
        $this->siteEvent = $siteEvent;
    }

    /**
     * Dispatches CMS request
     * @param Request $request
     * @param Response $response
     * @todo should we render UI in controller dispatch action?
     */
    public function dispatch(Request $request, Response $response = null)
    {
        if (!$this->siteEvent->getSite()) {
            throw new Exception\SiteNotFoundException(
                    sprintf("%s: Site not found for hostname '%s'.",
                            __METHOD__ , $this->siteEvent->getHost()));
        }

        $this->loadBackendConfig();

        $sm = $this->sm;
        /* @var $page \Vivo\UI\Page */
        $root = $sm->get('Vivo\CMS\UI\Root');
        $page = $sm->get('Vivo\UI\Page');


         if ($this->securityManager->getUserPrincipal())
         {
             $page->setMain($sm->get('Vivo\Backend\UI\Backend'));
         } else {
             $page->setMain($sm->get('Vivo\Backend\UI\Logon'));
         }




        $root->setMain($page);

        $this->tree->setRoot($root);
        $this->tree->loadState();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->tree->init(); //replace by lazy init
            //if request is  ajax call, we use result of method
            $result = $this->handleAction();
        } else {
            $this->tree->init();
            $this->handleAction();
            if (!$this->redirector->isRedirect()) {
                $result = $this->tree->view();
            }
        }

        $this->tree->saveState();
        $this->tree->done();

        if (!$this->redirector->isRedirect()) {
            $result = $this->tree->view();
        }


        if($this->redirector->isRedirect()){
            return $this->redirector->getResponse();
        } elseif ($result instanceof ModelInterface) {
            $this->mvcEvent->setViewModel($result);
        } elseif ($result instanceof InputStreamInterface) {
            //skip rendering phase
            $response->setInputStream($result);
            return $response;
        } elseif (is_string($result)) {
            //skip rendering phase
            $response->setContent($result);
            return $response;
        }
    }

    /**
     * Handles action on component.
     */
    protected function handleAction()
    {
        //TODO is a better way how to obtain params?
        //TODO create router for asembling and matching path of action
        $request = $this->getRequest();
        if (!$action = $request->getQuery('act')) {
            if (!$action = $request->getPost('act')) {
                return;
            } else {
                $params = $request->getPost('args', array());
            }
        } else {
            $params = $request->getQuery('args', array());
        }

        $parts = explode(Component::COMPONENT_SEPARATOR, $action);
        $action = array_pop($parts);
        $path = implode(Component::COMPONENT_SEPARATOR, $parts);
        return $this->tree->invokeAction($path, $action, $params);
    }

    public function loadBackendConfig()
    {
        $config = include __DIR__."/../../../config/backend.config.php";

        $cmsConfig = $this->sm->get('cms_config');
        unset($cmsConfig['ui']);
        $cmsConfig = ArrayUtils::merge($cmsConfig, $config);


        $this->sm->setAllowOverride(true);
        $this->sm->setService('cms_config', $cmsConfig);
        $this->sm->setAllowOverride(false);

        //reconfigure template resolver
        $this->sm->get('template_resolver')->configure($cmsConfig['templates']);

        //TODO reconfigure service manager

        return $config;
    }


    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->mvcEvent = $event;
    }

    /**
     * @return \Zend\Mvc\MvcEvent
     */
    public function getEvent()
    {
        return $this->mvcEvent;
    }

    /**
     * Sets ComponentTreeController
     * @param ComponentTreeController $tree
     */
    public function setComponentTreeController(ComponentTreeController $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return \Zend\Stdlib\RequestInterface
     */
    public function getRequest() {
        return $this->mvcEvent->getRequest();
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function setSM(ServiceManager $sm)
    {
        //TODO use ServiceManagerAwareInitializer instead this method
        $this->sm = $sm;
    }

    /**
     * Sets redirector.
     * @param \Vivo\Util\Redirector $redirector
     */
    public function setRedirector(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }
}
