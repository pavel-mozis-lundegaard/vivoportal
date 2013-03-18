<?php
namespace Vivo\CMS\Event;

use Vivo\CMS\Model;
use Zend\EventManager\Event;

/**
 * Description of CMSEvent
 */
class CMSEvent extends Event
{
    /**#@+
     * CMS events triggered by CMS front controller
     */
    const EVENT_FETCH_DOCUMENT  = 'fetch_document';
    const EVENT_CREATE          = 'create';
    const EVENT_LOAD            = 'load';
    const EVENT_INIT            = 'init';
    const EVENT_VIEW            = 'view';
    const EVENT_SAVE            = 'save';
    const EVENT_ERROR           = 'error';
    /**#@-*/

    /**
     * @var Model\Document
     */
    protected $document;

    /**
     *
     * @var string
     */
    protected $requestedPath;

    /**
     * @var Model\Site
     */
    protected $site;

    /**
     * @return Model\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     *
     * @param Model\Document $document
     */
    public function setDocument(Model\Document $document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function getRequestedPath()
    {
        return $this->requestedPath;
    }

    /**
     * @param string $requestedPath
     */
    public function setRequestedPath($requestedPath)
    {
        $this->requestedPath = $requestedPath;
    }
    /**
     * @return Model\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Model\Site $site
     */
    public function setSite(Model\Site $site)
    {
        $this->site = $site;
    }
}
