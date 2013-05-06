<?php
namespace Vivo\Util;

use Zend\EventManager\SharedEventManagerAwareInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Response;
use Zend\Http\Request;

/**
 * Redirector listens on shared event manager for redirect events and modify
 * response headers to invoke redirect.
 *
 * Only the first redirect event is processed, other events are ignored.
 * Redirect could be perfomed immediately, but it isn't common case.
 * Usually we want to finish dispatching current request.
 */
class Redirector implements SharedEventManagerAwareInterface
{
    /**
     * @var boolean
     */
    protected $redirect = false;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Shared event manager.
     * @var SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * Constructor.
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Whather current request will be redirected.
     * @return boolean
     */
    public function isRedirect()
    {
        return $this->redirect;
    }

    /**
     * Callback for redirect events.
     *
     * Modify response header for redirection.
     * @param \Vivo\Util\RedirectEvent $event
     * @return boolean Whether event was processed.
     */
    public function redirect(RedirectEvent $event)
    {
        if ($this->redirect == true) {
            return false;
        }

        $this->redirect = true;
        if (!$url = $event->getUrl()) {
            $url = $this->request->getUri()->getPath();
        }
        $this->response->setStatusCode($event->getParam('status_code') ?: Response::STATUS_CODE_302);
        $this->response->getHeaders()->addHeaderLine('Location', $url);
        if($event->getParam('immediately')) {
            //TODO - FixMe: The Zend\Http\Response object does not have the sendHeaders() method!
            $this->response->sendHeaders();
            die();
        }
        return true;
    }

    /**
     * Sets shared event manager and attach listener to redirect event.
     * @param \Zend\EventManager\SharedEventManagerInterface $sharedEventManager
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedEventManager = $sharedEventManager;
        $this->sharedEventManager->attach('*', RedirectEvent::EVENT_REDIRECT, array($this, 'redirect'));
    }

    /**
     * Unsets shared event manager.
     */
    public function unsetSharedManager()
    {
        $this->sharedEventManager = null;
        //TODO detach listener
    }

    public function getSharedManager()
    {
    }
}
