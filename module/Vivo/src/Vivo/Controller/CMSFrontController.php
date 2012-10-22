<?php
namespace Vivo\Controller;

use Zend\View\Model\ViewModel;
use Vivo\CMS\ComponentFactory;
use Vivo\CMS;
use Vivo\CMS\Model\Site;

use Zend\EventManager\EventInterface as Event;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
  */
class CMSFrontController implements DispatchableInterface,
    InjectApplicationEventInterface
{

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * @var \Vivo\CMS\CMS
     */
    private $cms;

    /**
     * @var \Vivo\Model\Site
     */
    private $site;

    /**
     * @var \Vivo\CMS\ComponentFactory
     */
    private $componentFactory;

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
    public function setCMS(CMS $cms) {
        $this->cms = $cms;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site) {
        $this->site = $site;
    }

    /**
     * Dispatch CMS request
     * @param Request $request
     * @param Response $response
     */
    public function dispatch(Request $request, Response $response = null)
    {
        //TODO: add exception when document doesn't exist
        //TODO: redirects based on document properties(https, $document->url etc.)

        $documentPath = $this->event->getRouteMatch()->getParam('path');
        $document = $this->cms->getDocument($documentPath, $this->site);
        $root = $this->componentFactory->getRootComponent($document);
        $root->init();
        $result = $root->view();
        $root->done();

        if ($result instanceof ViewModel) {
            $this->event->setViewModel($result);
        } else {
            //TODO shortcicruit if result is stream,
        }
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
}
