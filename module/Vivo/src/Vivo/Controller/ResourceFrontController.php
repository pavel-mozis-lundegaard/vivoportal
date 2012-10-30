<?php
namespace Vivo\Controller;

use Vivo\Controller\Exception;
use Vivo\IO\Exception\ExceptionInterface  as IOException;

use Vivo\IO\FileInputStream;

use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\EventManager\EventInterface as Event;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Response as HttpResponse;

/**
 * Controller for giving all resource files
 */
class ResourceFrontController implements DispatchableInterface,
    InjectApplicationEventInterface
{

    /**
     * @var Event
     */
    protected $event;

    public function dispatch(Request $request, Response $response = null)
    {
        //TODO find resource file by path and return it
        $path = $this->event->getRouteMatch()->getParam('path');
        $module = $this->event->getRouteMatch()->getParam('module');

        //TODO set apropriate headers

        if ($module === 'vivo') {
            //it's vivo core resource
            try {
                $resourceStream = new FileInputStream(__DIR__.'/../../../resources/'.$path);
            } catch (IOException $e) {
                throw new Exception\FileNotFoundException("Resource file not found.", null, $e);
            }
        } else {
            //TODO load resource from module
        }

        $response->setStream($resourceStream);
        return $response;
    }

    /* (non-PHPdoc)
     * @see Zend\Mvc.InjectApplicationEventInterface::setEvent()
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /* (non-PHPdoc)
     * @see Zend\Mvc.InjectApplicationEventInterface::getEvent()
     */
    public function getEvent()
    {
        return $this->event;
    }
}
