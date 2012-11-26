<?php
namespace Vivo\View\Helper;

use Vivo\CMS\CMS;
use Vivo\CMS\Model\Entity;
use Vivo\UI\Component;
use Vivo\View\Helper\Exception\InvalidArgumentException;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * View helper for gettting resource url.
 */
class Resource extends AbstractHelper
{
    /**
     * Helper options
     * @var array
     */
    private $options = array(
            'check_resource' => false, // usefull for debuging sites
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
    public function __construct(Url $urlhelper, CMS $cms, $options = array())
    {
        $this->urlHelper = $urlhelper;
        $this->cms = $cms;
        $this->options  = array_merge($this->options, $options);
    }

    public function __invoke($resourcePath, $source)
    {
        if ($this->options['check_resource'] == true) {
            $this->checkResource($resourcePath, $source);
        }
        if ($source instanceof Entity) {
            $entityUrl = $this->cms->getEntityUrl($source);
            $url = $this->urlHelper
                    ->__invoke('vivo/resource_entity',
                            array('path' => $resourcePath,
                                    'entity' => $entityUrl));
        } elseif (is_string($source)) {
            $url = $this->urlHelper
                    ->__invoke('vivo/resource',
                            array('source' => $source, 'path' => $resourcePath));
        } else {
            throw new InvalidArgumentException(
                    sprintf("%s: Invalid value for parameter 'source'.",
                            __METHOD__), $code, $previous);
        }

        //Replace encoded slashes in the url. It's needed because apache returns 404 when the url contains encoded slashes
        //This behvaior could be changed in apache config, but it is not possible to do that in .htacces context.
        //@see http://httpd.apache.org/docs/current/mod/core.html#allowencodedslashes
        $url = str_replace('%2F', '/', $url);

        return $url;
    }

    public function checkResource($resourcePath, $source)
    {
        //TODO check resoure and throw exception if doesn't exist.
    }
}
