<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Api\CMS;
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
    private $options = array();

    /**
     * @var CMS
     */
    private $cms;

    /**
     * @param Url $urlhelper
     */
    public function __construct(CMS $cms, $options = array())
    {
        $this->cms = $cms;
        $this->options = array_merge($this->options, $options);
    }

    public function __invoke(Model\Document $document)
    {
        $entityUrl = $this->cms->getEntityUrl($document);
        $urlHelper = $this->getView()->plugin('url');
        $url = $urlHelper(null, array('path' => $entityUrl), false);

        //Replace encoded slashes in the url. It's needed because apache returns 404 when the url contains encoded slashes
        //This behvaior could be changed in apache config, but it is not possible to do that in .htaccess context.
        //@see http://httpd.apache.org/docs/current/mod/core.html#allowencodedslashes
        $url = str_replace('%2F', '/', $url);
        return $url;
    }
}
