<?php
namespace Vivo\Controller;

use Vivo\CMS\CMS;
use Vivo\Controller\Exception;
use Vivo\IO\Exception\ExceptionInterface as IOException;
use Vivo\IO\FileInputStream;
use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\SiteManager\Event\SiteEvent;
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
     * @var CMS
     */
    protected $cms;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     *
     * @var SiteEvent
     */
    protected $siteEvent;

    public function dispatch(Request $request, Response $response = null)
    {
        $pathToResource = $this->event->getRouteMatch()->getParam('path');
        $source = $this->event->getRouteMatch()->getParam('source');
        if ($source === 'vivo') {
            //it's vivo core resource - the core resources should be moved into own module
            try {
                $resourceStream = new FileInputStream(
                        __DIR__ . '/../../../resource/' . $pathToResource);
            } catch (IOException $e) {
                throw new Exception\FileNotFoundException(
                        "Resource file not found.", null, $e);
            }
        } elseif ($source === 'entity') {
            //it's entity resource
            $entityPath = $this->event->getRouteMatch()->getParam('entity');
            $entity = $this->cms->getSiteEntity($entityPath, $this->siteEvent->getSiteModel());
            $resourceStream = $this->cms->readResource($entity, $pathToResource);
        } else {
            //it's module resource
            $resourceStream = $this->resourceManager
                    ->getResourceStream($source, $pathToResource);
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

    /**
     * @param CMS $cms
     */
    public function setCMS(CMS $cms) {
        $this->cms = $cms;
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

    /**
     * @param ResourceManager $resourceManager
     */
    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param Site $site
     */
    public function setSiteEvent(SiteEvent $siteEvent)
    {
        $this->siteEvent = $siteEvent;
    }
}
