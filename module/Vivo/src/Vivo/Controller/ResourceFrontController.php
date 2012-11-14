<?php
namespace Vivo\Controller;

use Vivo\Controller\Exception;
use Vivo\IO\Exception\ExceptionInterface as IOException;
use Vivo\IO\FileInputStream;
use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\Util;

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

    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    public function dispatch(Request $request, Response $response = null)
    {
        $pathToResource = $this->event->getRouteMatch()->getParam('path');
        $moduleName = $this->event->getRouteMatch()->getParam('module');

        if ($moduleName === 'vivo') {
            //it's vivo core resource
            try {
                $resourceStream = new FileInputStream(
                        __DIR__ . '/../../../resource/' . $pathToResource);
            } catch (IOException $e) {
                throw new Exception\FileNotFoundException(
                        "Resource file not found.", null, $e);
            }
        } elseif ($moduleName === 'entity') {
            //it's entity resource
            //TODO
        } else {
            //it's module resource
            $resourceStream = $this->resourceManager
                    ->getResourceStream($moduleName, $pathToResource);
        }
        $filename = pathinfo($pathToResource, PATHINFO_FILENAME);
        $ext = pathinfo($pathToResource, PATHINFO_EXTENSION);

        $mimeType = Util\MIME::getType($ext);
        $response->getHeaders()->addHeaderLine('Content-Type: ' . $mimeType)
                ->addHeaderLine(
                        'Content-Disposition: inline; filename="' . $filename
                                . ($ext ? ".$ext" : '') . '"');

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

    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }
}
