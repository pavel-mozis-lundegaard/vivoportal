<?php
namespace Vivo\View\Helper;

use Vivo\CMS\CMS;
use Vivo\CMS\Model;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * View helper for gettting document url
 */
class Document extends AbstractHelper
{
    /**
     * Helper options
     * @var array
     */
    private $options = array(
            );

    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @var CMS
     */
    private $cms;

    /**
     * @param Url $urlhelper
     */
    public function __construct(Url $urlhelper, CMS $cms,$options = array())
    {
        $this->urlHelper = $urlhelper;
        $this->cms = $cms;
        $this->options  = array_merge($this->options, $options);
    }

    public function __invoke(Model\Document $document)
    {
            $entityUrl = $this->cms->getEntityUrl($document);
            $url = $this->urlHelper
                    ->__invoke('vivo/cms',
                            array('path' => $entityUrl));
            $url = str_replace('%2F', '/', $url);
            return $url;
    }
}
