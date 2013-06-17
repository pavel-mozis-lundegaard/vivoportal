<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Event\CMSEvent;

use Zend\View\Helper\AbstractHelper;

/**
 * Class Cms
 * @package Vivo\View\Helper
 */
class Cms extends AbstractHelper
{
    /**
     * CMS Event
     * @var CMSEvent
     */
    protected $cmsEvent;

    /**
     * Constructor
     * @param CMSEvent $cmsEvent
     */
    public function __construct(CMSEvent $cmsEvent)
    {
        $this->cmsEvent = $cmsEvent;
    }

    /**
     * Invoke the helper as a PhpRenderer method call
     * @return mixed
     */
    public function __invoke($quickCmd = null)
    {
        if (is_null($quickCmd)) {
            return $this;
        }
        switch ($quickCmd) {
            case 'requestedDocument':
                $retVal = $this->getRequestedDocument();
                break;
            case 'site':
                $retVal = $this->getSite();
                break;
            case 'requestedPath':
                $retVal = $this->getRequestedPath();
                break;
            default:
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Unsupported quick command '%s'", __METHOD__, $quickCmd));
                break;
        }
        return $retVal;
    }

    /**
     * Returns requested document
     * @return \Vivo\CMS\Model\Document
     */
    public function getRequestedDocument()
    {
        return $this->cmsEvent->getDocument();
    }

    /**
     * Returns current site
     * @return \Vivo\CMS\Model\Site
     */
    public function getSite()
    {
        return $this->cmsEvent->getSite();
    }

    /**
     * Returns the requested path
     * @return string
     */
    public function getRequestedPath()
    {
        return $this->cmsEvent->getRequestedPath();
    }
}
