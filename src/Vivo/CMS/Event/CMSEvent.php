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
    const EVENT_FETCH_DOCUMENT      = 'cms_fetch_document';
    const EVENT_REDIRECT            = 'cms_redirect';
    const EVENT_CREATE              = 'cms_create';
    const EVENT_LOAD                = 'cms_load';
    const EVENT_INIT                = 'cms_init';
    const EVENT_VIEW                = 'cms_view';
    const EVENT_SAVE                = 'cms_save';
    const EVENT_RENDER              = 'cms_render';
    const EVENT_ERROR               = 'cms_error';
    const EVENT_FETCH_ERRORDOCUMENT = 'cms_fetch_errordocument';

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
     *
     * @var \Vivo\UI\ComponentInterface
     */
    protected $root;

    /**
     * @var
     */
    protected $result;

    /**
     * @var \Exception
     */
    protected $exception;

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
    public function setDocument(Model\Document $document = null)
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

    /**
     * @return \Vivo\UI\ComponentInterface
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param \Vivo\UI\ComponentInterface $root
     */
    public function setRoot(\Vivo\UI\ComponentInterface $root)
    {
        $this->root = $root;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
