<?php
namespace Vivo\CMS;

use Vivo\CMS\Api;
use Vivo\CMS\Api\CMS;
use Vivo\CMS\ComponentFactory;
use Vivo\CMS\Event\CMSEvent;
use Vivo\Controller\Exception;
use Vivo\IO\InputStreamInterface;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\UI\Component;
use Vivo\UI\ComponentTreeController;
use Vivo\Util\RedirectEvent;
use Vivo\Util\Redirector;
use Vivo\Util\UrlHelper;

use Zend\EventManager\EventInterface as Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface;

/**
 * The front controller which is responsible for dispatching all requests for documents in CMS repository.
 */
class FrontController implements DispatchableInterface,
                                 InjectApplicationEventInterface,
                                 EventManagerAwareInterface,
                                 ServiceLocatorAwareInterface
{
    /**
     * @var MvcEvent
     */
    protected $mvcEvent;

    /**
     * @var CMSEvent
     */
    protected $cmsEvent;

    /**
     * @var SiteEvent
     */
    protected $siteEvent;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var CMS
     */
    protected $cmsApi;

    /**
     * @var Api\Document
     */
    protected $documentApi;

    /**
     * @var ComponentFactory
     */
    protected $componentFactory;

    /**
     * @var ComponentTreeController
     */
    protected $tree;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    protected $request;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * Attaches default listeners.
     */
    public function attachListeners()
    {
        $this->events->attachAggregate($this->serviceManager->get('Vivo\CMS\FetchDocumentListener'), 100);
        $this->events->attachAggregate($this->serviceManager->get('Vivo\CMS\FetchDocumentByUrlListener'), 200);
    }

    /**
     * Dispatches CMS request.
     * @param Request $request
     * @param Response $response
     * @todo should we render UI in controller dispatch action?
     */
    public function dispatch(Request $request, Response $response = null)
    {
        $this->request = $request;
        $this->attachListeners();

        if (!$this->siteEvent->getSite()) {
            throw new Exception\SiteNotFoundException(
                    sprintf("%s: Site not found for hostname '%s'.",
                            __METHOD__ , $this->siteEvent->getHost()));
        }

        //Redirect when path does not end with a slash
        /** @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        $routeMatch = $this->getEvent()->getRouteMatch();
        if ($routeMatch->getMatchedRouteName() == 'vivo/cms') {
            $path   = $routeMatch->getParam('path');
            $lastChar   = substr($path, -1);
            if ($lastChar != '/') {
                $routeParams    = $routeMatch->getParams();
                $routeParams['path']    = $path . '/';
                $routeOptions   = array(
                    'query'     => $this->request->getQuery()->toArray(),
                );
                $url            = $this->urlHelper->fromRoute('vivo/cms', $routeParams, $routeOptions);
                $params         = array('status_code' => 301, 'immediately' => true);
                $this->events->trigger(new RedirectEvent($url, $params));

            }
        }


        //fetch document
        $eventResult = $this->events->trigger(CMSEvent::EVENT_FETCH_DOCUMENT, $this->getCmsEvent(),
            function ($result) {
            return ($result instanceof \Vivo\CMS\Model\Document);
        });


        $document = $eventResult->last();
        if (!$document) {
            throw new \Exception(sprintf('%s: Document for requested path `%s` can not be fetched.',
                        __METHOD__,
                        $this->cmsEvent->getRequestedPath()),
                    \Zend\Http\Response::STATUS_CODE_404);
        } else {
            $this->cmsEvent->setDocument($document);
        }

        if (!$this->documentApi->isPublished($document)) {
            throw new \Exception(sprintf('%s: Document `%s` is not published.',
                        __METHOD__,
                        $document->getPath()),
                    \Zend\Http\Response::STATUS_CODE_404);
        }

        //perform redirects
        $this->performRedirects($document);
        if ($this->redirector->isRedirect()){
            return $response;
        }

        //create ui component tree
        $root = $this->componentFactory->getRootComponent($document);

        //perform tree operations
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


        if ($this->redirector->isRedirect()) {
            return $response;
        }
        if ($result instanceof ModelInterface) {
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
     * Performs redirects based on document properties.
     * @param Model\Document $document
     * @return null
     */
    protected function performRedirects(Model\Document $document)
    {
        //redirect secured documents to https
        if ($document->getSecured() && $this->request->getUri()->getScheme() !== 'https') {
            $uri = clone $this->request->getUri();
            $uri->setScheme('https');
            $uri->setPort(null);
            $this->events->trigger(new RedirectEvent((string) $uri));
            return;
        }

        //redirect document with specific url
        if ($document->getUrl()) {
            if ($document->getUrlPrecedence() == true) {
                if($document->getUrl() != $this->cmsEvent->getRequestedPath()) {
                    $url = $this->urlHelper->fromRoute(null, array('path' => $document->getUrl()));
                    $this->events->trigger(new RedirectEvent($url));
                    return;
                }
            } else {
                if ($document->getUrl() == $this->cmsEvent->getRequestedPath()) {
                    $path = $this->cmsApi->getEntityRelPath($document);
                    $url = $this->urlHelper->fromRoute(null, array('path' => $path));
                    $this->events->trigger(new RedirectEvent($url));
                    return;
                }
            }
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

    /**
     * Returns CMS event
     * @return CMSEvent
     */
    public function getCmsEvent()
    {
        return $this->cmsEvent;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
    }

    /**
     * @return MvcEvent
     */
    public function getEvent()
    {
        return $this->mvcEvent;
    }

    /**
     * @param ComponentFactory $componentFactory
     */
    public function setComponentFactory(ComponentFactory $componentFactory)
    {
        $this->componentFactory = $componentFactory;
    }

    /**
     * @param CMS $cms
     */
    public function setCMS(CMS $cmsApi)
    {
        $this->cmsApi = $cmsApi;
    }

    /**
     * @param SiteEvent $site
     */
    public function setSiteEvent(SiteEvent $siteEvent)
    {
        $this->siteEvent = $siteEvent;
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
     * @return RequestInterface
     */
    public function getRequest() {
        return $this->mvcEvent->getRequest();
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
       return $this->events;
    }

    /**
     * Sets event manager
     * @param EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
        $this->events->addIdentifiers(__CLASS__);
    }

    /**
     * Returns redirector.
     * @return Redirector
     */
    public function getRedirector()
    {
        return $this->redirector;
    }

    /**
     * Inject redirector.
     * @param Redirector $redirector
     */
    public function setRedirector(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    /**
     *
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * Inject Service Manager.
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceManager) {
            $this->serviceManager = $serviceLocator;
        }
    }

    /**
     * Inject UrlHelper.
     * @param UrlHelper $urlHelper
     */
    public function setUrlHelper (UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Sets the CMS event
     * @param CMSEvent $cmsEvent
     * @return void
     */
    public function setCmsEvent(CMSEvent $cmsEvent)
    {
        $this->cmsEvent = $cmsEvent;
    }

    /**
     * Inject Document Api
     * @param \Vivo\CMS\Api\Document $documentApi
     */
    public function setDocumentApi(Api\Document $documentApi)
    {
        $this->documentApi = $documentApi;
    }
}
